=====
examples
=====

api methods

 - selectByStatus
 - selectById
 - selectStatusById
 - updateKeys
 - updateStatus
 - saveFile
 - updateTimeStamps
 - verifyByParams

site.com/api.php?action=selectByStatus&params[:status]=1

site.com/api.php?action=selectById&params[:cid]=1&params[:userid]=2

site.com/api.php?action=selectStatusById&params[:cid]=1&params[:userid]=2

site.com/api.php?action=updateKeys&params[:cert]=text&params[:key1]=1&params[:key2]=2&params[:status]=7&params[:userid]=8&params[:cid]=2

site.com/api.php?action=updateStatus&params[status]=4&params[:userid]=9637&params[:cid]=15

site.com/api.php?action=saveFile&params[file_name]=u123/1&params[date_end]=2016

site.com/api.php?action=verifyByParams&params[userid]=2&params[cid]=17&params[hash]=abs&params[sign]=123qa

site.com/api.php?action=updateTimeStamps&params[userid]=1&params[cid]=2