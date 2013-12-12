Collections
===========

Collections is a MODX Revolution Extra that adds a custom `CollectionContainer` resource class with the following behaviour:

1. Any direct child resource will be hidden from the Resource Tree in the Manager, and listed in a grid view (similar to Articles) under a dedicated "Children" tab.
2. Any children that themselves have children will be shown in the Tree, to be managed normally.

![Collections Children Grid](http://j4p.us/image/290M3v2I343c/Screen%20Shot%202013-11-20%20at%208.38.47%20PM.png)

####Sub Collections
Just like the MODX Resource Tree itself, Collections supports nesting. You can create a Collection within another Collection. Sub Collection Containers will be displayed in the resource tree and their children will be displayed in the grid view.

####Drag n Drop
You can drag n drop Resources into a Collections container and if they don't have children of their own they will be listed in the grid. If they do have children, they'll just remain in the Tree as usual.

####Alternatives
@goldsky recently released an Extra called "GridClassKey" that does similar things, without drag n drop and some other logic. BUT it does sport some very nifty Ext JS features, like Advanced Search of items in the grid. You have options.
