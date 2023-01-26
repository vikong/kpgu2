<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Container;

class ContainerService
{
    /**
     * Обрабатывает файл контейнера и сохраняет данные в БД
     * @param string $file  файл
     * @return Container    Контейнер - запись в БД
     */
    public static function ProcessFile(string $file): Container
    {
        $dbService = new DbService();
        $container = $dbService->CreateContainer(["name"=>$file, "success"=>false]);

        try {
            $proc = new ContainerProcessor();
            $proc->loadContainer($file);
    
            $data = array();
            $data[DbService::DocumentTitle] = $proc->extractDocumentTitle();
            $data[DbService::Events] = $proc->extractEvents();

            $dbService->Save($data, $container);
    
        } catch (\Throwable $th) {
            $container->error = $th->getMessage();
            $container->success = false;
            $container->save();
        }

        return $container;
    }

    /**
     * Обрабатывает все файлы в папке
     * @param string $scanFolder папка для обработки
     * @return void
     */
    public static function ProcessFolder(string $scanFolder)
    {
        $files = array_filter(scandir($scanFolder), function ($file) {
            global $scanFolder;
            return !is_dir("{$scanFolder}/{$file}");
        });

        foreach ($files as $key => $file) {
            ContainerService::ProcessFile("{$scanFolder}/{$file}");
        }
        //todo: что делать с файлом после обработки?

    }

}