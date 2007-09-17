<?php
$fileInstances=array();
class kfmFile extends kfmObject{
	var $ctime='';
	var $directory='';
	var $id=-1;
	var $mimetype='';
	var $name='';
	var $parent=0;
	var $path='';
	var $size=0;
	var $type;
	var $writable=false;
	function kfmFile(){
		global $kfm_db_prefix;
		if(func_num_args()==1){
			$this->id=func_get_arg(0);
			parent::kfmObject();
			$filedata=db_fetch_row("SELECT id,name,directory FROM ".$kfm_db_prefix."files WHERE id=".$this->id);
			$this->name=$filedata['name'];
			$this->parent=$filedata['directory'];
			$dir=kfmDirectory::getInstance($this->parent);
			$this->directory=$dir->path;
			$this->path=$dir->path.'/'.$filedata['name'];
			if(!file_exists($this->path)){
				$this->error('File cannot be found');
				$this->delete();
				return false;
			}
			$this->writable=$this->isWritable();
			$this->ctime=filectime($this->path);
			$mimetype=get_mimetype($this->path);
			$pos=strpos($mimetype,';');
			$this->mimetype=($pos===false)?$mimetype:substr($mimetype,0,$pos);
			$this->type=trim(substr(strstr($this->mimetype,'/'),1));
		}
	}
	function checkAddr($addr){
		return (
			strpos($addr,'..')===false&&
			strpos($addr,'.')!==0&&
			strpos($addr,'/')===false &&
			!in_array(preg_replace('/.*\./','',$addr),$GLOBALS['kfm_banned_extensions'])
			);
	}
	function getContent(){
		return ($this->id==-1)?false:utf8_encode(file_get_contents($this->path));
	}
	function getExtension(){
		/* Function that returns the extension of the file.
		 * if a parameter is given, the extension of that parameters is returned
		 * returns false on error.
		 */
		if(func_num_args()==1){
			$filename=func_get_arg(0);
		}else{
			if($this->id==-1)return false;
			$filename=$this->name;
		}
		$dotext=strrchr($filename,'.');
		if($dotext === false) return false;
		return strtolower(substr($dotext,1));
	}
	function getUrl($x=0,$y=0){
		global $rootdir, $kfm_userfiles_output,$kfm_workdirectory;
		$cwd=$this->directory.'/'==$rootdir?'':str_replace($rootdir,'',$this->directory);
		if(!file_exists($this->path))return 'javascript:alert("missing file")';
		if(preg_replace('/.*(get\.php)$/','$1',$kfm_userfiles_output)=='get.php'){
			if($kfm_userfiles_output=='get.php')$url=preg_replace('/\/[^\/]*$/','/get.php?id='.$this->id.GET_PARAMS,$_SERVER['REQUEST_URI']);
			else $url=$kfm_userfiles_output.'?id='.$this->id;
			if($x&&$y)$url.='&width='.$x.'&height='.$y;
		}
		else{
			if($this->isImage()&&$x&&$y){
				$img=kfmImage::getInstance($this);
				$img->setThumbnail($x,$y);
				return $kfm_userfiles_output.$kfm_workdirectory.'/thumbs/'.$img->thumb_id;
			}
			else $url=preg_replace('/([^:])\/\//','$1/',$kfm_userfiles_output.'/'.$cwd.'/'.$this->name); # TODO: check this line - $cwd may be incorrect if the requested file is from a search
		}
		return str_replace('//','/',$url);
	}
	function delete(){
		global $kfm_db_prefix,$kfm_allow_file_delete;
		if(!$kfm_allow_file_delete)$this->error(kfm_lang('permissionDeniedDeleteFile'));
		if(!kfm_cmsHooks_allowedToDeleteFile($this->id))$this->error(kfm_lang('CMSRefusesFileDelete',$this->path));
		if(!$this->hasErrors()){
			if(unlink($this->path)||!file_exists($this->path))$this->db->exec("DELETE FROM ".$kfm_db_prefix."files WHERE id=".$this->id);
			else $this->error('unable to delete file '.$this->name);
		}
		return !$this->hasErrors();
	}
	function getInstance($id=0){
		if(!$id)return false;
		global $fileInstances;
		if(!isset($fileInstances[$id]))$fileInstances[$id]=new kfmFile($id);
		return $fileInstances[$id];
	}
	function getSize(){
		if(!$this->size)$this->size=filesize($this->path);
		return $this->size;
	}
	function getTags(){
		global $kfm_db_prefix;
		$arr=array();
		$tags=db_fetch_all("select tag_id from ".$kfm_db_prefix."tagged_files where file_id=".$this->id);
		foreach($tags as $r)$arr[]=$r['tag_id'];
		return $arr;
	}
	function isImage(){
		return in_array($this->getExtension(),array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
	}
	function isWritable(){
		return (($this->id==-1)||!is_writable($this->path))?false:true;
	}
	function rename($newName){
		global $kfm_allow_file_edit,$kfm_db_prefix;
		if(!$kfm_allow_file_edit)return $this->error(kfm_lang('permissionDeniedEditFile'));
		if(!kfm_checkAddr($newName))return $this->error(kfm_lang('cannotRenameFromTo',$this->name,$newName));
		$newFileAddress=$this->directory.$newName;
		if(file_exists($newFileAddress))return $this->error(kfm_lang('fileAlreadyExists'));
		rename($this->path,$newFileAddress);
		$this->name=$newName;
		$this->db->query("UPDATE ".$kfm_db_prefix."files SET name='".addslashes($newName)."' WHERE id=".$this->id);
	}
	function setContent($content){
		global $kfm_allow_file_edit;
		if(!$kfm_allow_file_edit)return $this->error(kfm_lang('permissionDeniedEditFile'));
		$result=file_put_contents($this->path,utf8_decode($content));
		if(!$result)$this->error(kfm_lang('errorSettingFileContent'));
	}
	function setTags($tags){
		global $kfm_db_prefix;
		if(!count($tags))return;
		$this->db->exec("DELETE FROM ".$kfm_db_prefix."tagged_files WHERE file_id=".$this->id);
		foreach($tags as $tag)$this->db->exec("INSERT INTO ".$kfm_db_prefix."tagged_files (file_id,tag_id) VALUES(".$this->id.",".$tag.")");
	}
	function size2str(){
		# returns the size in a human-readable way
		# expects input size in bytes
	 	# if no input parameter is given, the size of the file object is returned 
		$size=func_num_args()?func_get_arg(0):$this->getSize();
		if(!$size)return '0';
		$format=array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
		$n=floor(log($size)/log(1024));
		return $n?round($size/pow(1024,$n),1).' '.$format[$n]:'0 B';
	}
	function addToDB($filename, $directory_id){
		global $kfm_db_prefix;
		$sql="insert into ".$kfm_db_prefix."files (name,directory) values('".addslashes($filename)."',".$directory_id.")";
		$q=$this->db->query($sql);
		return $this->db->lastInsertId($kfm_db_prefix.'files','id');
	}
}
?>
