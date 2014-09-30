Collections
===========

Collections is a MODX Revolution Extra that adds a custom `CollectionContainer` resource class with the following behaviour:

1. Any direct child resource will be hidden from the Resource Tree in the Manager, and listed in a grid view (similar to Articles) under a dedicated "Children" tab, for which the label can be customized.
2. Any children that themselves have children will be shown in the Tree, to be managed normally.

![Collections Children Grid](http://sepiariver.co/Zp3u3l)

####Sub Collections
Just like the MODX Resource Tree itself, Collections supports nesting. You can create a Collection within another Collection. Sub Collection Containers will be displayed in the resource tree and their children will be displayed in the grid view.

####Drag n Drop
You can drag n drop Resources into a Collections container and if they don't have children of their own they will be listed in the grid. If they do have children, they'll just remain in the Tree as usual.

###Custom Views
As of version 2.x, Collections supports customizable views. Views are configured in Collections Custom Manager Page (CMP):

![Collections CMP] (http://sepiariver.co/Zp3xwg)
![Collections New View] (http://sepiariver.co/1Bz07Tg)

###Resources
The official documentation for Collections can be found here: rtfm.modx.com/extras/revo/collections
