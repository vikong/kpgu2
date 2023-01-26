# Предметная область
## Таблицы

| Таблица | Описание |
| ------- | -------- |
| [containers](#containers) | Контейнеры |
| [document_titles](#document_titles) | Заголовок Документа |
| [processes](#processes) | Информация О Процессе |
| [events](#events) | Сообщение О Событии |
| [event_types](#event_types) | Типы Событий |
| [event_information](#event_information) | Информация О Событии |
| [agents](#agents) | Агенты |
| [event_agents](#event_agents) | Агенты Получатели |
| [event_items](#event_items) | Предметы События |
| [item_types](#item_types) | Виды Предмета |
| [owner_types](#owner_types) | Виды Владельца |
| [item_states](#item_states) | СостояниеПредмета |
| [coordination_types](#coordination_types) | ВидДанныхДляКоординации |
| [coordinations](#coordinations) | ДанныеДляКоординации |

**Примечание** ~~зачёркнутые поля~~ не использовать, введены временно, для удобства 

### containers 
**Контейнеры**
Обработанные контейнеры
| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| Name | Файл контейнера |
| Document_titles_id | Ссылка на добавленный документ |
| Success | Признак успешной обработки |
| Error | Описание ошибки |

### document_titles 
**Заголовок Документа**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title   | Название |
| document_reference | Ссылка Документа |
| document_presentation | Представление Документа |
| creator_reference | Ссылка Создателя |
| creator_presentation | Представление Создателя |
| creation_time | Время Создания |

### processes 
**Информация О Процессе**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Название |
| process_reference | Ссылка Процесса |
| process_presentation | Представление Процесса |
| creator_reference | Ссылка Создателя |
| creator_presentation | Представление Создателя |
| creation_time | Время Создания |

### events 
**Сообщение О Событии**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Имя |
| document_title_id | Документ |
| event_information_id | Информация о событии |
| process_id | Информация О Процессе |

### event_types 
**Типы Событий**
| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title   | Наименование |

### event_information 
**Информация О Событии**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Название |
| event_reference | Ссылка события |
| event_presentation | Представление события |
| event_time | Время События |
| state | СтатусСобытия |

### agents 
**Агенты**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| agent_reference | Ссылка агента |
| view | Представление |

### event_agents 
**Агенты Cобытия**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| type  | Тип |
|   | {1-отправитель}  |
|   | {2-получатель}  |
| title | узел |
| view | ДанныеДляВизуализации |
| owner_type_id | ВидВладельца |
| event_id | сообщение о событии |
| agent_id | агент |

### event_items
**Предметы События**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | наименование |
| item_type_id | Вид предмета |
| item_state_id | Состояние педмета |
| owner_type_id | Вид владельца |


### item_types
**Виды Предмета**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Название |


### owner_types
**Виды Владельца**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Название |


### item_states
**СостояниеПредмета**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| title | Название |


### coordination_types
**ВидДанныхДляКоординации**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| name | Название |
| data_type | тип данных |


### coordinations
**ДанныеДляКоординации**

| Поле | Описание |
| ---- | -------- |
| Id   | PK       |
| coordination_type_id | ВидДанныхДляКоординации |
| event_item_id | Предметы События |
| value | значение |

