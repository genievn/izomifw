<?php
class UploadController extends Object
{

    public function defaultCall()
    {


    }

    public function upload()
    {
        global $abs;

        $render = $this->getTemplate('dummy');

        $path = @$_POST['path'];
        if (!$path) $path = config('root.temp_folder');

        $relative_path = str_replace(".",DIRECTORY_SEPARATOR,$path);
        $http_relative_path = str_replace(".","/",$path);
        # full http path
        $http_relative_path = config('root.url').$http_relative_path;

        $upload_path = $abs.$relative_path.DIRECTORY_SEPARATOR;

        if (!is_dir($upload_path))
        # path is not an existed directory, so return false
        {
            $render->setSuccess(false);
            return;
        }



        foreach( $_FILES as $Fileid){
            $upload_name = @$_POST['upload_name'];
            # if no upload_name is specified, use the original name (converted to slug)
            if (!$upload_name) $upload_name = basename($Fileid['name']);
            $parts = pathinfo($upload_name);

            $extension = $parts['extension'];
            $basename = str_replace(".$extension", "", $parts['basename']);

            $upload_name = $this->slug($basename).".".$extension;

            $target_path = $upload_path . $upload_name;
            if( move_uploaded_file($Fileid['tmp_name'], $target_path) ) {
                $render->setSuccess(true);
                $render->setExtension($extension);
                $render->setName($basename.".".$extension);
                # uploaded http link
                $render->setPath($http_relative_path."/".$upload_name);
            }else{
                $render->setSuccess(false);
            }
        }

        return $render;
    }

    private function slug($str)
    {
	    $str = strtolower(trim($str));
	    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
	    $str = preg_replace('/-+/', "-", $str);
	    return $str;
    }
}
?>
