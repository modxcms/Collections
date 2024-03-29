Changelog for Collections.

Collections 4.1.0
===================
- Refactor Fred integration

Collections 4.0.0
===================
- Support for MODX Revolution 3.0.0
- Fix Fred endpoints
- Fix quick update action
- Fix view for Create New
- Fetch distinct resources for collection

Collections 3.7.1
===================
- Fix lexicon typo
- Fix editing from grid

Collections 3.7.0
===================
- Make pagination stateful
- Add CollectionsOnResourceSort custom event
- Add quick create button
- Sync with crowdin
- Fred integration
- Don't makeURL for deleted resources & hide view button
- Allow duplicating children
- Fix TV search in selections
- Check permissions before rendering specific action button / context menu action
- Handle edit and view child as classic link, allowing to cmd/ctrl click to open in new tab
- Fix clear filter clearing current folder
- Remove 'overflow: hidden' to title on main column view to correct visual bug [#219]

Collections 3.6.0
===================
- Add sync tables resolver
- Added 3 options to speed up large child lists (+10K resources)
- Fix D&D sort for Resources in depth > 1

Collections 3.5.0
===================
- Add collections.renderer.boolean renderer
- Add AjaxManager compatibility
- Adjust position of other columns when creating/updating column with position specified
- Add excludeResources option to getSelections snippet
- Prevent Collection's view reset after quick resource update
- Use lexicons for the Open button
- Add overflow ellipse to the main-column
- Add area to system settings

Collections 3.4.2
===================
- Fix saving tv values from update from grid
- Fix after save event fired on update from grid

Collections 3.4.1
===================
- Fix Safari endless reloading
- Fix Selections when sorting by menuindex

Collections 3.4.0
===================
- Pass column name to snippet renderer
- Prevent permanent sort breaking D&D sort
- Add Collections.renderer.buttons renderer
- Allow snippet renderer on non-existing column
- Add event names to update from grid processor
- Show folders under collections
- Show breadcrumbs when browsing folders under collections
- Import / Export collection views
- Add quick update for collection & selection children
- Prevent saving columns with snippet renderer from grid
- Add new system settings to show create collection/selection button in tree tool bar
- Display resource id in link resource window
- Adjust colors in collections grid

Collections 3.3.0
===================
- Show assigned templates in Collection's view grid
- Fix ignore parents for pdoResources
- Add "Condition for Link resource window" as a setting for Sellection view
- Fix displaying ContentBlocks when content is in separate tab
- Fix checking for template usage in views

Collections 3.2.2
===================
- Fix D&D sort for Collection's view columns
- Added Collections.renderer.pagetitleWithIcons
- Improved handler for inline action buttons allowing icons
- Fix D&D sort when menuindex is set as default sort field
- Rename String sort type

Collections 3.2.1
===================
- Fix update from previous versions

Collections 3.2.0
===================
- Add sort type as a new Collection's template column setting
- Add permanent sort
- Auto set column label from TV caption or Tagger group name
- Pass columns value as input to snippet renderer
- Add sort type as a new Collection's view setting
- Add content disposition as a new Collection's view setting
- Add parent as a new Collection's view setting
- Make TV columns searchable
- Use modx->getTableName to get table names for Tagger tables
- Show TV's default value in grid
- Clear filter use sort field & dir from view
- Prevent error while not passing Resource templates in Collection view

Collections 3.1.1
===================
- Fixed showing (empty) template in child's template select box
- Fixed renderer image path
- Fixed D&D reorder children when element or file tree is selected
- Fixed back to collection/selection button for static resources/weblinks/symlinks
- Fixed back to collection/selection button on create new child page

Collections 3.1.0
===================
- Fixed reset filter button in Selections
- Added option to set default values for content type for new children
- Added system setting to modify image path in image renderer
- Allow children under Selections
- Added an option to add quip column
- Fixed selecting templates in Collection view in Revo 2.2.x
- Added option to set default values for hidemenu,published,cacheable,searchable and richtext for new children
- Added option to specify sortby for Selections Resource search query
- Added option to set label for Back to Collection/Selection button
- Fixed displaying selection grid with TVs
- Pass whole row to snippet renderer
- Fixed sorting by TV with dash in name

Collections 3.0.2
===================
- Fixed saving Collections view from Resource's settings tab
- Link Resource to Selection can be done by searching ID
- Fixed passing sort dir int uppercase format to getSelections
- Removed parents option from getSelections call

Collections 3.0.1
===================
- Fixed update from grid

Collections 3.0.0
===================
- Added validation for column name if contains a dot
- Added PHP renderer (snippet) that will be used for a column
- Added view option to define Resource Classes that will be available in resource type select
- Added view option to define context menu items
- Added view option to define buttons and their style
- Fixed saving view options from not-activated tab
- Changed default tree icon for Collections in Revo 2.3
- Added getSelections snippet (works with getPage, getCache, etc.)
- Updated view of Collection's settings tab
- Updated Collections view
- Added CRC for Selections
- Added back button to Collection's children and revert close button to original functionality
- Fire OnBeforeEmptyTrash and OnEmptyTrash when removing Resource via Collections

Collections 2.2.2
===================
- Fixed rendering TV and Tagger columns with dash in name/alias

Collections 2.2.1
===================
- Fixed PHP 5.2 compatibility

Collections 2.2.0
===================
- Added an option to set position of Content field
- Added an options to set Tab's and New child's button label
- Added Collections.renderer.image for rendering images
- Added tabs to Collection views to split its settings
- Splitted Setting tab to vtabs for Resource settings and Collections settings
- Improved close button in collection's childs
- Fixed showing data in TV columns
- Added an option to permanently remove deleted resource
- Fixed duplicate action from context menu
- Added data controller to show resource owerview page
- Fix # being appended to manager url when clicking a button
- Fix strict standards error (PHP 5.4+) in resource/getlist processor
- Fix not working editlink on pagetitle click in grid

Collections 2.1.0
===================
- Added option to duplicate Collection's view
- Updated layout for create/update view's column
- Added view,update,delete,duplicate,publish items to children's grid context menu
- Added children default template, default resource type and allow resource type selection options to collection's templates
- Fixed View button
- Fixed logging messages from plugin

Collections 2.0.2
===================
- Fixed datetime renderers

Collections 2.0.1
===================
- Fixed saving collections container in Revolution 2.2.x

Collections 2.0.0
===================
- Added collections templates
- Added ability to create different child type from grid
- Support for Revolution 2.3

Collections 1.3.3
===================
- Remove debugger call :X

Collections 1.3.2
===================
- Hotfix for confirm navigation dialog
- Fixed selecting multiple rows
- Fixed checking for Tagger

Collections 1.3.1
===================
- Release with correct version number

Collections 1.3.0
===================
- Support for Tagger in search field in Children tab
- Added ability to drag and drop child to resource tree to change parent
- English and German lexicon updates

Collections 1.2.0
===================
- Added ability to search via created by full name
- Added ability to search via created by username
- Fixed switching child between two Collections
- Fixed switching great parent to Collections when moving last child
- Fixed child name after creating a duplicate
- Added drag & drop sort by menuindex

Collections 1.1.1
===================
- #20 Fixed after re-save child set show_in_tree 1

Collections 1.1.0
===================
- #9 Added duplicate button
- #10 Added icon for Collection into the Resource tree
- #11 Make grid stateful and added some more columns (until clearing cache grid keeps showed/hidden columns and their order)
- Renamed "Collection container" to "Collection"
- #7 Added German translation (thanks to pepebe)
- #12 Added Czech translation
- #14 Added Dutch translation (thanks to @mark_hamstra)
- #15 Added French translation (thanks to @rtripault)
- #16 #17 Added Russian translation (thanks to vanchelo)
- #18 Fixed show_in_tree conflict

Collections 1.0.0
===================
- Published in MODX extras

Collections 0.8.2
===================
- Finished renaming CollectionsContainer -> CollectionContainer
- Removed chromephp log calls

Collections 0.8.1
===================
- Fixed showing aliases in children grid

Collections 0.8.0
===================
- Renamed CollectionsContainer to CollectionContainer
- Fixed returning proper count of children under Collection Container

Collections 0.7.0
===================
- Added switchback resolver that will switch all Collections Containers back to modDocument and set show_in_tree to 1 for all Collections children on uninstall
- Added support for handle class_key switch from CollectionContainer and to CollectionContainer

Collections 0.6.0
===================
- Fixed proper showing Collections Container under another Collections Container
- Fixed proper showing normal containers with children under CRC
- Added listener for Before Empty Trash event to hide Resources that are under Collections Container and that will not have other children after the trash will be cleaned

Collections 0.5.0
===================
- Updated the plugin to inject JS for handling cancel button in Resource Update panel

Collections 0.4.0
===================
- Added plugin that handles correct setting of show_in_tree parameter for Resources after creating a new Resource or sorting resources

Collections 0.3.0
===================
- Visual improvements for grid with children

Collections 0.2.0
===================
- Extended Resource Update panel with new Tab that contains grid with children


Collections 0.1.0
===================
- Initial release.
