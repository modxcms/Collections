var iconClass = {
    "edit": "icon-pencil",
    "quickupdate": "icon-edit",
    "delete": "icon-trash-o",
    "duplicate": "icon-files-o",
    "publish": "icon-thumbs-o-up",
    "unpublish": "icon-thumbs-down",
    "view": "icon-eye",
    "open": "icon-folder-open-o",
    "remove": "icon-ban",
    "undelete": "icon-undo",
    "unlink": "icon-chain-broken"
};

var pagetitleWithButtons = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    +'<span class="collections-children-icon x-tree-node-collapsed"><i class="{icons}"></i></span><h3 class="main-column buttons"><a href="javascript:void(0);" data-id="{id}" data-action="edit" title="Edit {pagetitle}">{pagetitle}</a></h3>'
    +'<ul class="actions">'
    +'<tpl for="actions">'
    +'<li><a href="javascript:void(0);" class="controlBtn {className}">{text}</a></li>'
    +'</tpl>'
    +'</ul>'
    +'</div></tpl>',{
    compiled: true
});

var pagetitle = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    + '<span class="collections-children-icon x-tree-node-collapsed"><i class="{icons}"></i></span><h3 class="main-column buttons"><a href="javascript:void(0);" data-id="{id}" data-action="edit" title="Edit {pagetitle}">{pagetitle}</a></h3>'
    + '</div></tpl>', {
    compiled: true
});

var pagetitleLink = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    + '<a href="javascript:void(0);" data-id="{id}" data-action="edit" title="Edit {pagetitle}">{pagetitle}</a>'
    + '</div></tpl>', {
    compiled: true
});

var pagetitleWithIcons = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    +'<span class="collections-children-icon x-tree-node-collapsed"><i class="{icons}"></i></span><h3 class="main-column buttons"><a href="javascript:void(0);" data-id="{id}" data-action="edit" title="Edit {pagetitle}">{pagetitle}</a></h3>'
    +'<ul class="actions">'
    +'<tpl for="actions">'
    +'<li><a href="javascript:void(0);" class="controlBtn {className}" title="{text}">'
    +'<i class="icon icon-fw icon {[ iconClass[values.key] ]}" data-id="{parent.id}" data-action="{values.key}"></i>{[ iconClass[values.key] ? "":values.text ]}'
    +'</a></li>'
    +'</tpl>'
    +'</ul>'
    +'</div></tpl>',{
    compiled: true
});

var icons = new Ext.XTemplate('<tpl for=".">'
    +'<ul class="actions solo">'
    +'<tpl for="actions">'
    +'<li><a href="javascript:void(0);" class="controlBtn {className}" title="{text}">'
    +'<i class="icon icon-fw icon {[ iconClass[values.key] ]}" data-id="{parent.id}" data-action="{values.key}"></i>{[ iconClass[values.key] ? "":values.text ]}'
    +'</a></li>'
    +'</tpl>'
    +'</ul>'
    +'</tpl>',{
    compiled: true
});

Collections.renderer.buttons = function(value, metaData, record, rowIndex, colIndex, store) {
    return icons.apply(record.data);
};

Collections.renderer.qtip = function(value, metaData, record, rowIndex, colIndex, store) {
    metaData.attr = 'ext:qtip="' + value + '"';
    return value;
};

Collections.renderer.pagetitleWithButtons = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitleWithButtons.apply(record.data);
};

Collections.renderer.pagetitleWithIcons = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitleWithIcons.apply(record.data);
};

Collections.renderer.pagetitle = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitle.apply(record.data);
};

Collections.renderer.pagetitleLink = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitleLink.apply(record.data);
};

Collections.renderer.datetimeTwoLines = function(value, metaData, record, rowIndex, colIndex, store) {
    if (value == 0) return '';

    var d = Date.parseDate(value, 'Y-m-d H:i:s');

    var date = Ext.util.Format.date(d, MODx.config['collections.mgr_date_format']);
    var time = Ext.util.Format.date(d, MODx.config['collections.mgr_time_format']);

    return '<div class="collections-grid-date">' + date + '<span class="collections-grid-time">' + time + '</span></div>';
};

Collections.renderer.datetime = function(value, metaData, record, rowIndex, colIndex, store) {
    if (value == 0) return '';

    var d = Date.parseDate(value, 'Y-m-d H:i:s');

    return Ext.util.Format.date(d,MODx.config['collections.mgr_datetime_format']);
};

Collections.renderer.timestampToDatetime = function(value, metaData, record, rowIndex, colIndex, store) {
    if (value == 0 || value == null) return '';

    return Ext.util.Format.date(new Date(parseInt(value)),MODx.config['collections.mgr_datetime_format']);
};

Collections.renderer.image = function(value, metaData, record, rowIndex, colIndex, store) {
    if (value != '' && value != null) {
        var imgPath = MODx.config['collections.renderer_image_path'];
        return '<img src="' + MODx.config.base_url + imgPath + value + '" width="64">';
    }
};