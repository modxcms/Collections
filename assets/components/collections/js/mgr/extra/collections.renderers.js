var pagetitleWithButtons = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    +'<h3 class="main-column buttons"><a href="{action_edit}" title="Edit {pagetitle}">{pagetitle}</a></h3>'
    +'<tpl if="actions">'
    +'<ul class="actions">'
    +'<tpl for="actions">'
    +'<li><a href="javascript:void(0);" class="controlBtn {className}">{text}</a></li>'
    +'</tpl>'
    +'</ul>'
    +'</tpl>'
    +'</div></tpl>',{
    compiled: true
});

var pagetitle = new Ext.XTemplate('<tpl for="."><div class="collections-title-column">'
    + '<h3 class="main-column"><a href="{action_edit}" title="Edit {pagetitle}">{pagetitle}</a></h3>'
    + '</div></tpl>', {
    compiled: true
});

Collections.renderer.qtip = function(value, metaData, record, rowIndex, colIndex, store) {
    metaData.attr = 'ext:qtip="' + value + '"';
    return value;
};

Collections.renderer.pagetitleWithButtons = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitleWithButtons.apply(record.data);
};

Collections.renderer.pagetitle = function(value, metaData, record, rowIndex, colIndex, store) {
    return pagetitle.apply(record.data);
};

Collections.renderer.pagetitleLink = function(value, metaData, record, rowIndex, colIndex, store) {
    return '<a href="' + record.data.action_edit + '" title="Edit ' + value + '">' + value + '</a>';
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