// see license.txt for licensing
function kfm_changeDirectory(id){
	if(id=='kfm_directory_icon_0')id='kfm_directory_icon_1';
	var el=$(id),a,els=$$('td.kfm_directory_open');
	if(!el)return;
	setTimeout('$clear(window.dragTrigger);',1);
	if(window.ie)while(el&&!el.node_id)el=el.parentNode;
	kfm_cwd_name=el.kfm_directoryname;
	kfm_cwd_id=el.node_id;
	for(var a=0;a<els.length;++a)els[a].removeClass('kfm_directory_open');
	el.parentNode.className+=' kfm_directory_open';
	setTimeout('x_kfm_loadFiles(kfm_cwd_id,kfm_refreshFiles);x_kfm_loadDirectories(kfm_cwd_id,kfm_refreshDirectories);',20);
}
function kfm_createDirectory(id){
	if(!kfm_vars.permissions.dir.mk)return kfm.alert('permission denied: cannot create directory');
	kfm_prompt(kfm.lang.CreateDirMessage(kfm_directories[id].path),kfm.lang.NewDirectory,function(newName){
		if(newName&&newName!=''&&!/\/|^\./.test(newName))x_kfm_createDirectory(id,newName,kfm_refreshDirectories);
	});
}
function kfm_deleteDirectory(id){
	if(!kfm_vars.permissions.dir.rm)return kfm.alert('permission denied: cannot delete directory');
	if(!kfm.confirm(kfm.lang.DelDirMessage(kfm_directories[id].path)))return;
	if(kfm_directories[id].hasChildren && !kfm.confirm(kfm.lang.RecursiveDeleteWarning(kfm_directories[id].name)))return;
	x_kfm_deleteDirectory(id,kfm_deleteDirectoryCheck);
}
function kfm_deleteDirectoryCheck(res){
	if(res.type&&res.type=='error'){
		switch(parseInt(res.msg)){
			case 1: kfm_log('error: '+kfm.lang.IllegalDirectoryName(res.name)); break;
			case 2:{ // not empty
				var ok=kfm.confirm(kfm.lang.RecursiveDeleteWarning(res.name));
				if(ok)x_kfm_deleteDirectory(res.id,1,kfm_deleteDirectoryCheck);
				break;
			}
			case 3: kfm_log('error: '+kfm.lang.RmdirFailed(res.name)); break;
			case 4: kfm_log('error: '+kfm.lang.DirectoryNotInDb); break;
			default: new Notice(res.msg);
		}
	}
	else{
		var is_found=0,p=res.oldpid;
		while(p&&!is_found){
			if(p==kfm_cwd_id)is_found=1;
			p=kfm_directories[p].parent;
		}
		if(is_found)kfm_changeDirectory('kfm_directory_icon_'+p);
		kfm_refreshDirectories(res);
	}
}
function kfm_dir_addLink(t,name,parent_addr,is_last,has_node_control,parent){
	var r=kfm.addRow(t),c,pdir=parent_addr+name,name=(name==''?'root':name);
	var name_text=(new Element('span',{
		'id':'directory_name_'+parent
	})).setHTML('0');
	var el=$extend(
		kfm.addEl(new Element('div',{
			'id':'kfm_directory_icon_'+parent,
			'class':'kfm_directory_link '+(kfm_cwd_name==pdir?'':'kfm_directory_open')
		}),name_text),
		{kfm_directoryname:pdir,node_id:parent}
	).setStyles('cursor:'+(window.ie?'hand':'pointer'));
	kfm_addContextMenu(el,function(e){
		var links=[],i,node_id=this.node_id;
		links.push(['kfm_renameDirectory("'+node_id+'")',kfm.lang.RenameDir,'',!kfm_vars.permissions.dir.ed]);
		links.push(['kfm_createDirectory("'+node_id+'")',kfm.lang.CreateSubDir,'folder_new',!kfm_vars.permissions.dir.mk]);
		if(node_id!=1)links.push(['kfm_deleteDirectory("'+node_id+'")',kfm.lang.DeleteDir,'remove',!kfm_vars.permissions.dir.rm]);
		if(kfm_return_directory)links.push(['setTimeout("window.close()",1);window.opener.SetUrl("'+kfm_directories[node_id].realpath+'/");','send to CMS']); // TODO: New String
		e=new Event(e);
		kfm_createContextMenu(e.page,links);
	});
	var cell=kfm.addCell(r,0,0,(
		has_node_control?
			newLink('javascript:kfm_dir_openNode('+parent+')','[+]','kfm_dir_node_'+parent,'kfm_dir_node_closed'):
			(new Element('span',{'id':'kfm_dir_node_'+parent})).setHTML('&nbsp;')
		),'kfm_dir_lines_'+(is_last?'lastchild':'child'));
	cell.style.width='16px';
	kfm.addCell(r,1,0,el,'kfm_dir_name');
	el.addEvent('click',function(e){
		e=new Event(e);
		if(e.rightClick)return;
		kfm_changeDirectory(this.id);
	});
	el.addEvent('mouseout',function(){
		kfm_directory_over=0;
		this.removeClass('hovered');
	});
	el.addEvent('mouseover',function(){
		if(!kfm_directory_over)kfm_directory_over=parseInt(this.node_id);
	});
	if(parent!=1)el.addEvent('mousedown',(function(id){
		return function(e){
			e=new Event(e);
			if(e.rightClick)return;
			document.addEvent('mouseup',kfm_dir_dragFinish);
			$clear(window.dragTrigger);
			window.dragTrigger=setTimeout(function(){
				kfm_dir_dragStart(id);
			},100);
		};
	})(parent));
	{ // fix name width
		var reqHeight=name_text.offsetHeight;
		name_text.innerHTML='. '+name;
		el=name_text;
		el.style.position='absolute';
		if(reqHeight&&el.offsetHeight>reqHeight){
			el.title=name;
			kfm_shrinkName(name,el,el,'offsetHeight',reqHeight,'');
		}
		else el.innerHTML=name;
		if(!window.ie)el.style.position='inherit';
	}
	{ // subdir holder
		r=kfm.addRow(t);
		kfm.addCell(r,0,0,' ',is_last?0:'kfm_dir_lines_nochild');
		kfm.addCell(r,1).id='kfm_directories_subdirs_'+parent;
	}
	return t;
}
function kfm_dir_drag(e){
	if(!window.dragType||window.dragType!=3)return;
	e=new Event(e);
	var m=e.page;
	clearSelections();
	window.drag_wrapper.setStyles('display:block;left:'+(m.x+16)+'px;top:'+m.y+'px');
}
function kfm_dir_dragFinish(e){
	$clear(window.dragTrigger);
	if(!window.dragType||window.dragType!=3)return;
	window.dragType=0;
	document.removeEvent('mousemove',kfm_dir_drag);
	document.removeEvent('mouseup',kfm_dir_dragFinish);
	var dir_from=window.drag_wrapper.dir_id;
	window.drag_wrapper.remove();
	window.drag_wrapper=null;
	dir_to=kfm_directory_over;
	if(dir_to==0||dir_to==dir_from)return;
	if(!kfm_vars.permissions.dir.mv)return kfm.alert('permission denied: cannot move directory');
	x_kfm_moveDirectory(dir_from,dir_to,kfm_refreshDirectories);
	kfm_selectNone();
}
function kfm_dir_dragStart(pid){
	window.dragType=3;
	var w=window.getSize().size;
	window.drag_wrapper=$extend(
		new Element('div',{
			'id':'kfm_drag_wrapper',
			'class':'directory',
			'styles':{
				'display':'none',
				'opacity':'.7'
			}
		}),
		{dir_id:pid}
	);
	window.drag_wrapper.appendText($('kfm_directory_icon_'+pid).kfm_directoryname);
	kfm.addEl(document.body,window.drag_wrapper);
	document.addEvent('mousemove',kfm_dir_drag);
}
function kfm_dir_openNode(dir){
	var node=$('kfm_dir_node_'+dir);
	node.className='kfm_dir_node_opened';
	node.href=node.href.replace(/open/,'close');
	$('kfm_directories_subdirs_'+dir).empty().appendText(kfm.lang.Loading);
	x_kfm_loadDirectories(dir,kfm_refreshDirectories);
}
function kfm_dir_closeNode(dir){
	var node=$('kfm_dir_node_'+dir);
	node.className='kfm_dir_node_closed';
	node.href=node.href.replace(/close/,'open');
	$('kfm_directories_subdirs_'+dir).empty();
}
function kfm_refreshDirectories(res){
	if(res.toString()===res)return kfm_log(res);
	var d=res.parent;
	if(d==1){ // root node
		var p=$('kfm_directories');
		p.parentNode.replaceChild(kfm_dir_addLink(new Element('table',{
			'id':'kfm_directories'
		}),'','',1,0,1),p);
		kfm_directories[1]={
			'parent':0,
			'name':'root',
			'path':'/',
			'realpath':res.properties.path,
			'hasChildren':res.directories.length
		}
		$('kfm_directory_icon_1').parentNode.className+=' kfm_directory_open';
	}
	var t=new Element('table'),n='kfm_dir_node_'+d;
	t.setStyle('table-layout','fixed');
	dirwrapper=$('kfm_directories_subdirs_'+d).empty();
	kfm.addEl(dirwrapper,t);
	var dirs=$A(res.directories);
	dirs.each(function(dir,a){
		kfm_dir_addLink(t,dir[0],res.reqdir,l=(a==dirs.length-1),dir[1],dir[2]);
		if(!kfm_directories[dir[2]])kfm_directories[dir[2]]={
			'parent':res.parent,
			'name':dir[0],
			'path':res.reqdir+dir[0],
			'realpath':res.properties.path+dir[0]+'/',
			'hasChildren':dir[1]
		};
	});
	if(d!='')kfm.addEl($($(n).parentNode).empty(),dirs.length?
		newLink('javascript:kfm_dir_closeNode("'+res.parent+'")','[-]',n,'kfm_dir_node_open'):
		(new Element('span',{
			'id':n
		})).setHTML(' ')
	);
	kfm_cwd_subdirs[d]=res.directories;
	if(!kfm_cwd_subdirs[d])kfm_dir_openNode(res.parent);
	kfm_setDirectoryProperties(res.properties);
	kfm_selectNone();
	kfm_log(kfm.lang.DirRefreshed);
	kfm_directories[kfm_cwd_id]=res.properties;
	kfm_directories[d].hasChildren=1;
	kfm_refreshPanels('kfm_left_column');
}
function kfm_renameDirectory(id){
	var directoryName=kfm_directories[id].name;
	kfm_prompt(kfm.lang.RenameTheDirectoryToWhat(directoryName),directoryName,function(newName){
		if(!newName||newName==directoryName)return;
		kfm_directories[id]=null;
		kfm_log(kfm.lang.RenamedDirectoryAs(directoryName,newName));
		x_kfm_renameDirectory(id,newName,kfm_refreshDirectories);
	});
}
function kfm_setDirectoryProperties(properties){
	if(!$('kfm_directory_properties'))return;
	var wrapper=$('kfm_directory_properties').empty();
	wrapper.properties=properties;
	var table=new Element('table'),row,cell,i;
	{ // directory name
		i=properties.allowed_file_extensions.length?properties.allowed_file_extensions.join(', '):kfm.lang.NoRestrictions;
		row=kfm.addRow(table);
		kfm.addCell(row,0,0,(new Element('strong')).setHTML(kfm.lang.Name));
		kfm.addCell(row,1,0,'/'+kfm_cwd_name);
	}
	{ // allowed file extensions
		i=properties.allowed_file_extensions.length?properties.allowed_file_extensions.join(', '):kfm.lang.NoRestrictions;
		row=kfm.addRow(table);
		kfm.addCell(row,0,0,(new Element('strong')).setHTML(kfm.lang.AllowedFileExtensions));
		kfm.addCell(row,1,0,i);
	}
	kfm.addEl(wrapper,table);
}
