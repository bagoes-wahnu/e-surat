var setMenu = function(menu=null, submenu=null) {
    if(menu != null && menu != ''){
        $(menu).addClass('kt-menu__item--active');
    }

    if(submenu != null && submenu != ''){
        $(submenu).addClass('kt-menu__item--active');
    }
}

var cekFileGambar = function(target, fileName='', maxFileSize=2097152) { /* maxFileSize in bytes */
	var temp = $(target);

    if(fileName != ''){
        var fileinfo = temp[0].files[0];

        console.log(formatBytes(fileinfo.size));
    	if(fileinfo.size > maxFileSize){
    		swal('Pemberitahuan', 'Ukuran maksimal file adalah '+formatBytes(maxFileSize)+'. Silahkan pilih file lagi atau file tidak akan disimpan', 'warning');
            $(target).val('');
            return false;
    	}else{
	        var ext = fileName.split(".")[fileName.split(".").length - 1];
	        if ((ext.toUpperCase() == "JPG") || (ext.toUpperCase() == "JPEG") || (ext.toUpperCase() == "PNG")){
	            return true;
	        }else {
	            swal('Pemberitahuan', 'File yang diterima adalah JPG, JPEG & PNG dengan ukuran maksimal '+formatBytes(maxFileSize)+'. Silahkan pilih file lagi atau file tidak akan disimpan', 'warning');
	            $(target).val('');
	            return false;
	        }
	    }
    }
    
    return true;
}