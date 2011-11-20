1. For resizing a collection of images
======================================
<iz:image:resize>
<img src="<iz:insert:url/>extra/share/images/xuka.jpg" width="100"/>
<img src="<iz:insert:url/>extra/share/images/noimage.png" scale="20"/>
</iz:image:resize>

Note: work properly with JPG, don't use PNG here
The domain name should not be localhost, instead change DNS in hosts file: 127.0.0.1 izportal.com


2. For layout a collection of images
<iz:image:layout height="100">
<img src="<iz:insert:url/>extra/share/images/xuka.jpg" />
<img src="<iz:insert:url/>extra/share/images/xuka.jpg" />
</iz:image:layout>


NOTE:
	+ All the images will be have the thumbnail created at cache folder :config('root.cache_folder')/portal/izo/images/