<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Agent;
use App\Models\Container;
use App\Models\Coordination;
use App\Models\CoordinationType;
use App\Models\DocumentTitle;
use App\Models\EventAgent;
use App\Models\EventInformation;
use App\Models\EventItem;
use App\Models\Event;
use App\Models\EventType;
use App\Models\ItemState;
use App\Models\ItemType;
use App\Models\OwnerType;
use App\Models\Process;
use Illuminate\Support\Facades\DB;

/**
 * Class DbService
 */
class DbService
{
    const DocumentTitle = "DocumentTitle";

    const ProcessInformation = "ProcessInformation";
    const Events = "Events";
    const EventInformation = "EventInformation";

    const Coordination = "coordination";

    const Sender = "sender";

     /**
      * Сохраняет данные одного контейнера
      * @param string $containerName
     * @param array $data
     *
     * @return Container
     */

    public function Save(array $data, Container $container): Container
    {
        DB::beginTransaction();
        try {
            $doc = $this->SaveDocumentTitle($data);
    
            // сообщения
            $this->SaveDocumentEvents($data, $doc);
    
            $container->document_title_id = $doc->getKey();
            $container->success = true;
            $container->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        return $container;

    }
    public static function CreateContainer(array $data): Container
    {
        $container = new Container($data);
        $container->save();
        return $container;
    }

    /**
     * Сохраняет Заголовок Документа
     * @param array $data
     * 
     * @return DocumentTitle
     */

    public function SaveDocumentTitle(array $data): DocumentTitle
    {
        $doc = new DocumentTitle($data[self::DocumentTitle]);
        $doc->save();
        return $doc;
    }



    /**
     * Сохраняет СообщенияДокумента (события)
     * @param array $data
     * @param DocumentTitle $doc
     * 
     * @return void
     */
    public function SaveDocumentEvents(array $data, DocumentTitle $doc): void
    {
        foreach ($data[self::Events] as $key => $event) 
        {
            $this->SaveEvent($event, $doc);
        }
    }

    /**
     * Сохраняет Событие(Сообщение) Документа
     * @param array $data
     *
     * @return void
     */

    public function SaveEvent(array $data, DocumentTitle $doc)
    {
        
        $event=new Event();
        $event->title = $data["title"];
        $event->document_title_id = $doc->getKey();
        
        $procInfo = $this->saveProcessInformation($data[self::ProcessInformation]);
        $eventInfo = $this->saveEventInformation($data[self::EventInformation]);

        $event->event_information_id = $eventInfo->getKey();
        $event->process_id = $procInfo->getKey();
        $event->save();

        // Агент-отправитель
        $this->saveEventAgent($data["sender"], EventAgent::SENDER, $event);

        // Агенты-получатели
        $recievers = array();
        foreach ($data["recievers"] as $key => $agentData) {
            $recievers[$key] = $this->saveEventAgent($agentData, EventAgent::RECIEVER);
        }
        $event->EventAgents()->saveMany($recievers);

        // Предметы
        $this->SaveEventItems($data["items"], $event);
    }

    public function saveEventAgent(array $data, int $type, Event $event=null): EventAgent
    {
        $agent = $this->SaveAgent($data);
        $owner = $this->saveOwnerType($data);

        $eventAgent = new EventAgent();
        $eventAgent->type = $type;
        $eventAgent->title = $data["title"];
        $eventAgent->owner_type_id = $owner->getKey();
        $eventAgent->agent_id = $agent->getKey();
        
        if(isset($event)){
            $event->EventAgents()->save($eventAgent);
        }

        return $eventAgent;
    }


    /**
     * Сохраняет список предметов для события документа
     * @param array $data
     *
     * @return EventItem[]
     */
    private function SaveEventItems(array $data, Event $event): array
    {
        
        $eventItems = array();

        foreach ($data as $key => $item) 
        {
            $eventItems[$key] = $this->SaveItem($item, $event);
        }

        return $eventItems;
    }

    /**
     * Сохраняет ИнформациюОПроцессе
     * @param array $data
     *
     * @return Process
     */
    public function saveProcessInformation($data): Process
    {
        //todo: информация о вышестоящем
        //var_dump($data);
        $procInfo = Process::where('process_reference', $data["process_reference"])->first();
        if (isset($procInfo)){
            return $procInfo;
        }
        $procInfo = new Process($data);
        $procInfo->save();
        return $procInfo;
    }


    /**
     * Сохраняет ИнформациюОСобытии
     * @param array $data
     *
     * @return EventInformation
     */
    public function saveEventInformation($data): EventInformation
    {
        $eventType=$this->saveEventType($data);
        $eventInformation = new EventInformation($data);
        $eventInformation->event_type_id = $eventType->getKey();
        $eventInformation->save();
        return $eventInformation;
    }

    /**
     * Сохраняет Тип События
     * 
     */
    public function saveEventType($data): EventType
    {
        $title = $data["title"];
        $eventType = EventType::where('title',$title)->first();
        if(isset($eventType)){
            return $eventType;
        }
        $eventType=new EventType();
        $eventType->title = $title;
        $eventType->save();
        return $eventType;
    }

     /**
      * Сохраняет Предмет События
     * @param array $data
     *
     * @return EventItem
     */
    private function SaveItem($data, Event $event): EventItem
    {
        $eventItem = new EventItem();

        $eventItem->title = $data["title"];

        $eventItem->event_id = $event->getKey();

        $itemType = $this->saveItemType($data);
        $eventItem->item_type_id = $itemType->getKey();

        $itemState = $this->saveItemState($data);
        $eventItem->item_state_id = $itemState->getKey();

        $ownerType = $this->saveOwnerType($data);
        $eventItem->owner_type_id = $ownerType->getKey();

        $eventItem->save();

        // Данные для координации
        $this->SaveCoordination($data, $eventItem);

        return $eventItem;

    }

    /**
     * Сохраняет ДанныДляКоординации
     */
    public function SaveCoordination(array $data, EventItem $eventItem)
    {
        if (isset($data[self::Coordination])){
            foreach ($data[self::Coordination] as $key => $value) {
                if (count($value)>0)
                {
                    $coordata = $this->SaveCoordinationData($value, $eventItem);
                }
            }
        }
    }

     /**
     * Сохраняет в справочник СостояниеСобытия
     * @param array $data
     *
     * @return ItemState
     */
    public function saveItemState($data): ItemState
    {
        if (!empty($data["item_state"])) {
            $title = $data["item_state"];
        }
        else
        {
            $title = "[отсутствует]";
        }
        $itemState = ItemState::where("title", $title)->first();
        if (isset($itemState)){
            return $itemState;
        }

        $itemState = new ItemState();
        $itemState->title = $title;
        $itemState->save();

        return $itemState;
    } 

     /**
      * Сохраняет в справочник ТипПредмета
     * @param array $data
     *
     * @return ItemType
     */
    public function saveItemType(array $data): ItemType
    {
        $title = $data["title"];
        $itemType = ItemType::where("title", $title)->first();
        if (isset($itemType)){
            return $itemType;
        }

        $itemType = new ItemType();
        $itemType->title = $title;
        $itemType->save();

        return $itemType;
    } 
    
     /**
      * Сохраняет в справочник ТипВладельца
     * @param array $data
     *
     * @return OwnerType
     */
    public function saveOwnerType($data): OwnerType
    {
        $title = $data["owner_type"];
        $ownerType = OwnerType::where("title", $title)->first();
        if (isset($ownerType)){
            return $ownerType;
        }

        $ownerType = new OwnerType();
        $ownerType->title = $title;
        $ownerType->save();

        return $ownerType;
    } 

    /**
     * Сохраняет ДанныеДляКоординации
     * @param array $data
     *
     * @return Coordination
     */
    private function SaveCoordinationData(array $data, EventItem $eventItem): Coordination
    {
        $rec=new Coordination();
        $rec->value = $data["value"];
        $rec->json = $data["json"];

        $rec->event_item_id=$eventItem->getKey();

        $c_type = $this->SaveCoordinationType($data);
        $rec->coordination_type_id = $c_type->getKey();

        $rec->save();
        return $rec;
    }

    /**
     * Сохраняет в справочник ТипДанныхДляКоординации
     */
    public function SaveCoordinationType(array $data): CoordinationType
    {
        $c_type = CoordinationType::where('name', $data["name"])->first();
        if (isset($c_type))
        {
            return $c_type;
        }
        $c_type = new CoordinationType();
        $c_type->name = $data["name"];
        $c_type->data_type = $data["data_type"];
        $c_type->save();

        return $c_type;
}

    /**
     * Сохраняет в справочник Агента
     * @param array $data
     *
     * @return Agent
     */
    private function SaveAgent($data): Agent
    {
        $agent=Agent::where('agent_reference', $data["agent_reference"])->first();
        if (isset($agent))
        {
            return $agent;
        }

        $agent = new Agent();

        $agent->agent_reference = $data["agent_reference"];
        $agent->view = $data["view"];

        $agent->save();

        return $agent;
    }

}

