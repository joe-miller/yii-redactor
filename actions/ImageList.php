<?php

/**
 * Redactor widget image list action.
 *
 * @param string $attr Model attribute
 */
class ImageList extends CAction {

    public $uploadPath;
    public $uploadUrl;
    private $foldersArray = array();

    public function run($attr) {
        $files = NULL;
        $attr = '/' . $attr;
        $name = strtolower($this->getController()->getId());
        $attribute = strtolower((string) $attr);

        if ($this->uploadPath === null) {
            $path = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads';
            $this->uploadPath = realpath($path);
            if ($this->uploadPath === false) {
                exit;
            }
        }
        if ($this->uploadUrl === null) {
            $this->uploadUrl = Yii::app()->request->baseUrl . '/uploads';
        }

        $attributePath = str_replace('//', '/', $this->uploadPath.$attribute);
        $attributeUrl = str_replace('//', '/', $this->uploadUrl.$attribute);
        
        if ($attr !== '/')
        {
            $files = CFileHelper::findFiles($attributePath, array('fileTypes' => array('gif', 'png', 'jpg', 'jpeg'), 'level'=>0));
        }
        
        $folders = $this->getFolders($this->uploadPath);
        foreach($folders as $folder)
        {
            if ($folder !=='.' && $folder !=='..')
            $data[] = array(
                'folder' => $folder,
            );
        }
        
        if ($files) {
            foreach ($files as $file) {
                $data[] = array(
                'thumb' => str_replace('//', '', $attributeUrl . '/' . pathinfo($file, PATHINFO_BASENAME)),
                'image' => str_replace('//', '', $attributeUrl . '/' . pathinfo($file, PATHINFO_BASENAME)),
                );
            }
        }
        echo CJSON::encode($data);
        exit;
    }
    
    private function getFolders($path)
    {
        $folders = scandir($path);
        foreach ($folders as $folder)
        {
            if($folder !== '.' && $folder !=='..' && $folder[0] !== '.' && $folder !== 'tmp')
            {
                if (is_dir($path . '/' . $folder))
                {
                    $this->foldersArray[] = substr(str_replace(realpath(Yii::app()->basePath . '/../images/'), '', $path) . '/' . $folder, 1);
                    $this->getFolders($path . '/' . $folder);
                }
            }
        }
        return $this->foldersArray;
    }

}
