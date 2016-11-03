<?php
/**
 * Default English Lexicon Entries for Collections
 *
 * @package collections
 * @subpackage lexicon
 */
$_lang['collections'] = 'Kolekce';

$_lang['collections.menu.collection_templates'] = 'Zobrazení kolekcí';
$_lang['collections.menu.collection_templates_desc'] = 'Definice zobrazení pro výpisy záznamů v kolekcích.';

// Settings lexicons
$_lang['setting_collections.mgr_date_format'] = 'Formát datumu ve výpisu záznamů';
$_lang['setting_collections.mgr_date_format_desc'] = 'Mohou být použity hodnoty viz <a href="http://docs.sencha.com/extjs/3.4.0/#!/api/Date" target="_blank">http://docs.sencha.com/extjs/3.4.0/#!/api/Date</a>.';
$_lang['setting_collections.mgr_time_format'] = 'Formát času ve výpisu záznamů';
$_lang['setting_collections.mgr_time_format_desc'] = 'Mohou být použity hodnoty viz <a href="http://docs.sencha.com/extjs/3.4.0/#!/api/Date" target="_blank">http://docs.sencha.com/extjs/3.4.0/#!/api/Date</a>.';
$_lang['setting_collections.mgr_datetime_format'] = 'Formát datumu a času ve výpisu záznamů';
$_lang['setting_collections.mgr_datetime_format_desc'] = 'Mohou být použity hodnoty viz <a href="http://docs.sencha.com/extjs/3.4.0/#!/api/Date" target="_blank">http://docs.sencha.com/extjs/3.4.0/#!/api/Date</a>.';
$_lang['setting_collections.user_js'] = 'Uživatelský JS soubor';
$_lang['setting_collections.user_js_desc'] = 'URL k uživatelskému javascript souboru obsahujícímu vlastní renderery atp.';
$_lang['setting_collections.user_css'] = 'Uživatelský CSS soubor';
$_lang['setting_collections.user_css_desc'] = 'URL k uživatelskému CSS souboru obsahujícímu vlastní styly.';
$_lang['setting_mgr_tree_icon_collectioncontainer'] = 'Ikona kolekcí';
$_lang['setting_mgr_tree_icon_collectioncontainer_desc'] = 'CSS třída, která se použije pro kolekce';
$_lang['setting_collections.renderer_image_path'] = 'Renderer image path';
$_lang['setting_collections.renderer_image_path_desc'] = 'Image path that will be used for Image renderer. Path will be appended to base_url.';
$_lang['setting_mgr_tree_icon_selectioncontainer'] = 'Selections icon';
$_lang['setting_mgr_tree_icon_selectioncontainer_desc'] = 'CSS class that will be used for Selections';
$_lang['setting_collections.tree_tbar_collection'] = 'Tree Tool Bar - Collection';
$_lang['setting_collections.tree_tbar_collection_desc'] = 'Show "New Collection" button in Tree tool bar';
$_lang['setting_collections.tree_tbar_selection'] = 'Tree Tool Bar - Selection';
$_lang['setting_collections.tree_tbar_selection_desc'] = 'Show "New Selection" button in Tree tool bar';


// System lexicons
$_lang['collections.system.type_name'] = 'Kolekce';
$_lang['collections.system.text_create'] = 'Kolekci';
$_lang['collections.system.text_create_here'] = 'Kolekci';
$_lang['collections.system.new_container'] = 'Nová kolekce';
$_lang['collections.system.new_selection_container'] = 'Nový výběr';
$_lang['collections.system.all'] = 'Vše';

// Global lexicons
$_lang['collections.global.search'] = 'Hledat';
$_lang['collections.global.change_order'] = 'Změna pořadí pro: [[+child]]';
$_lang['collections.global.change_parent'] = 'Změna složky pro: [[+child]]';
$_lang['collections.global.use_default'] = 'Použít výchozí';
$_lang['collections.global.import'] = 'Importovat';

// Children
$_lang['collections.children'] = 'Záznamy';
$_lang['collections.children.create'] = 'Vytvořit záznam';
$_lang['collections.children.view'] = 'Zobrazit záznam';
$_lang['collections.children.edit'] = 'Upravit záznam';
$_lang['collections.children.quickupdate'] = 'Quick Update';
$_lang['collections.children.publish'] = 'Publikovat záznam';
$_lang['collections.children.unpublish'] = 'Ukončit publikování záznamu';
$_lang['collections.children.duplicate'] = 'Zkopírovat záznam';
$_lang['collections.children.delete'] = 'Odstranit záznam';
$_lang['collections.children.undelete'] = 'Obnovit záznam';
$_lang['collections.children.delete_confirm'] = 'Opravdu chcete odstranit tento záznam?';
$_lang['collections.children.publish_multiple'] = 'Publikovat vybrané';
$_lang['collections.children.unpublish_multiple'] = 'Ukončit publikování vybraných';
$_lang['collections.children.delete_multiple'] = 'Odstranit vybrané';
$_lang['collections.children.delete_multiple_confirm'] = 'Opravdu chcete odstranit všechny vybrané záznamy?';
$_lang['collections.children.undelete_multiple'] = 'Obnovit vybrané';
$_lang['collections.children.none'] = 'Tento dokument nemá žádné záznamy.';
$_lang['collections.children.err_ns_multiple'] = 'Musíte vybrat alespoň jeden záznam.';
$_lang['collections.children.menuindex'] = 'Index řazení';
$_lang['collections.children.remove_action'] = 'Odstranit';
$_lang['collections.children.remove'] = 'Odstranit záznam';
$_lang['collections.children.remove_confirm'] = 'Opravdu chcete odstranit tento záznam?<br /><strong>Tato změna je nevratná.</strong>';
$_lang['collections.children.back_to_collection_label'] = 'Zpět do kolekce';

// Template
$_lang['collections.template.page_title'] = 'Zobrazení kolekcí';
$_lang['collections.template.templates'] = 'Zobrazení';
$_lang['collections.template.templates_desc'] = 'Možnosti zobrazení výpisu záznamů poté co uživatel klikne na kolekci ve stromu dokumentů. Zobrazení mohou obsahovat výchozí políčka, TV a vlastní TV jako např. políčka komponenty Tagger. Hodnoty kolekcí mohou být konfigurovány, tak aby byly buď samostatně nebo hromadně editovatelné přímo z výpisu.';
$_lang['collections.template.none'] = 'Zatím nejsou žádné zobrazení kolekcí.';
$_lang['collections.template.name'] = 'Název';
$_lang['collections.template.description'] = 'Popis';
$_lang['collections.template.add'] = 'Přidat zobrazení';
$_lang['collections.template.new_template'] = 'Nové zobrazení';
$_lang['collections.template.update_template'] = 'Upravit zobrazení';
$_lang['collections.template.remove'] = 'Odstranit zobrazení';
$_lang['collections.template.remove_confirm'] = 'Opravdu chcete odstranit toto zobrazení?';
$_lang['collections.template.update'] = 'Upravit zobrazení';
$_lang['collections.template.default_for_templates'] = 'Výchozí pro šablony';
$_lang['collections.template.bulk_actions'] = 'Povolit hromadné akce';
$_lang['collections.template.allow_dd'] = 'Povolit drag & drop';
$_lang['collections.template.page_size'] = 'Velikost stránky';
$_lang['collections.template.sort_field'] = 'Pole pro třídění';
$_lang['collections.template.sort_dir'] = 'Směr řazení';
$_lang['collections.template.set_as_global'] = 'Nastavit jako výchozí zobrazení';
$_lang['collections.template.global_template'] = 'Výchozí zobrazení';
$_lang['collections.template.template'] = 'Zobrazení kolekce';
$_lang['collections.template.empty'] = '(použít výchozí zobrazení)';
$_lang['collections.template.child_template'] = 'Výchozí šablona záznamů';
$_lang['collections.template.child_resource_type'] = 'Výchozí typ dokumentu záznamů';
$_lang['collections.template.resource_type_selection'] = 'Výběr typu dokumentu';
$_lang['collections.template.duplicate'] = 'Zkopírovat zobrazení';
$_lang['collections.template.general_settings'] = 'Obecná nastavení';
$_lang['collections.template.children_settings'] = 'Nastavení záznamů';
$_lang['collections.template.tab_label'] = 'Titulek záložky';
$_lang['collections.template.button_label'] = 'Text tlačítka nového záznamu';
$_lang['collections.template.content_place_original'] = 'Původní';
$_lang['collections.template.content_place_in_tab'] = 'V záložce';
$_lang['collections.template.content_place_none'] = 'Žádný';
$_lang['collections.template.content_place'] = 'Umístění obsahu';
$_lang['collections.template.content_place_original_except_children'] = 'Původní vyjma záznamů';
$_lang['collections.template.selections_settings'] = 'Nastavení výběru';
$_lang['collections.template.selection_enabled'] = 'Použití jako výběr';
$_lang['collections.template.view_for'] = 'Zobrazení pro';
$_lang['collections.template.view_for_all'] = 'Kolekce a výběry';
$_lang['collections.template.view_for_collections'] = 'Kolekce';
$_lang['collections.template.view_for_selections'] = 'Výběry';
$_lang['collections.template.collections_settings'] = 'Nastavení kolekce';
$_lang['collections.template.link_label'] = 'Text tlačítka pro nový odkaz';
$_lang['collections.template.context_menu'] = 'Položky kontextové nabídky';
$_lang['collections.template.context_menu_desc'] = '<strong>Platné hodnoty:</strong><br />view<br />edit<br />duplicate<br />publish<br />unpublish<br />delete<br />undelete<br />remove<br />unlink<br />-';
$_lang['collections.template.buttons'] = 'Tlačítka';
$_lang['collections.template.buttons_desc'] = 'Toto nastavení se použije pouze pro renderery používající akční tlačítka.<br />Pro přidání vlastní CSS třídy k tlačítku můžete vložit hodnotu ve tvaru <strong>:classname vlastnitrida</strong>.<br /><strong>Platné hodnoty:</strong><br />view<br />edit<br />duplicate<br />publish<br />unpublish<br />delete<br />undelete<br />remove<br />unlink';
$_lang['collections.template.allowed_resource_types'] = 'Platné typy dokumentu';
$_lang['collections.template.allowed_resource_types_desc'] = 'Zadejte platné typy dokumentu pro výběry.<br /><strong>Výchozí typy dokumentů:</strong><br />modDocument<br />modStaticResource<br />modSymLink<br />modWebLink<br /><br /><strong>Vlastí typy dokumentů:</strong><br />CollectionContainer<br />SelectionContainer';
$_lang['collections.template.back_to_collection'] = 'Text tlačítka zpět do kolekce';
$_lang['collections.template.back_to_selection'] = 'Text tlačítka zpět do výběru';
$_lang['collections.template.selection_create_sort'] = 'Řazení dokumentů při připojování do výběru';
$_lang['collections.template.child_hide_from_menu'] = 'Ve výchozím stavu skrýt záznamy z menu';
$_lang['collections.template.child_published'] = 'Ve výchozím stavu publikovat záznamy';
$_lang['collections.template.child_cacheable'] = 'Ve výchozím stavu ukládat záznamy do cache';
$_lang['collections.template.child_searchable'] = 'Ve výchozím stavu označit záznam jako vyhledatelný';
$_lang['collections.template.child_richtext'] = 'Ve výchozím stavu použít pro záznamy WYSIWYG';
$_lang['collections.template.child_content_type'] = 'Výchozí typ obsahu záznamů';
$_lang['collections.template.parent'] = 'Parent-id (optional, @SNIPPET can be used)';
$_lang['collections.template.child_content_disposition'] = 'Default children\'s content disposition';
$_lang['collections.template.sort_type'] = 'Typ řazení';
$_lang['collections.template.permanent_sort_before'] = 'Permanent sort - Before';
$_lang['collections.template.permanent_sort_after'] = 'Permanent sort - After';
$_lang['collections.template.selection_link_condition'] = 'WHERE condition for Link resource window';
$_lang['collections.template.selection_link_condition_desc'] = 'xPDO podmínka v JSON formátu.';
$_lang['collections.template.export_more'] = 'Exportovat označené zobrazení';
$_lang['collections.template.export'] = 'Exportovat zobrazení';
$_lang['collections.template.import'] = 'Importovat zobrazení';

$_lang['collections.template.column.none'] = 'Toto zobrazení nemá definované sloupce.';
$_lang['collections.template.column.name'] = 'Název';
$_lang['collections.template.column.label'] = 'Označení';
$_lang['collections.template.column.add'] = 'Přidat sloupec';
$_lang['collections.template.column.update'] = 'Upravit sloupec';
$_lang['collections.template.column.remove'] = 'Odstranit sloupec';
$_lang['collections.template.column.remove_confirm'] = 'Opravdu chcete odstranit tento sloupec?';
$_lang['collections.template.column.hidden'] = 'Skrytý';
$_lang['collections.template.column.sortable'] = 'Řaditelný';
$_lang['collections.template.column.width'] = 'Šířka';
$_lang['collections.template.column.position'] = 'Pozice';
$_lang['collections.template.column.editor'] = 'Editor';
$_lang['collections.template.column.renderer'] = 'Renderer';
$_lang['collections.template.column.php_renderer'] = 'Snippet renderer';
$_lang['collections.template.column.sort_type'] = 'Typ řazení';

$_lang['collections.template.sort_type_default'] = 'Výchozí';
$_lang['collections.template.sort_type_integer'] = 'Celé číslo';
$_lang['collections.template.sort_type_decimal'] = 'Desetinné číslo';
$_lang['collections.template.sort_type_datetime'] = 'Datum a Čas';

$_lang['collections.err.parent_ns'] = 'Rodič není specifikován.';
$_lang['collections.err.template_ns'] = 'Zobrazení není specifikováno.';
$_lang['collections.err.bad_sort_column'] = 'Řazení výpisu podle <strong>[[+ sloupec]]</strong> pomocí drag & drop řazení.';
$_lang['collections.err.clear_filter'] = 'Aby jste mohli používat drag & drop řazení zrušte aktivní <strong>Filtr</strong> a <strong>Vyhledávání</strong>.';
$_lang['collections.err.common'] = 'Během akce došlo k chybě.';
$_lang['collections.err.template_ns_name'] = 'Není zadán název.';
$_lang['collections.err.column_ns_name'] = 'Není zadán název.';
$_lang['collections.err.column_ae_name'] = 'Sloupec s tímto názvem již existuje.';
$_lang['collections.err.template_resource_template_aiu_s'] = 'Šablona <strong>[[+templates]]</strong> je již přiřazena k zobrazení kolekcí.';
$_lang['collections.err.template_resource_template_aiu_p'] = 'Šablony <strong>[[+templates]]</strong> jsou již přiřazeny k zobrazení kolekcí.';
$_lang['collections.err.cant_remove_id_column'] = 'Sloupec ID nelze odstranit. Tento sloupec je vyžadován, pokud tento sloupec nechcete zobrazovat nastavte možnost <strong>Skrytý</strong> na <strong>Ano</strong>.';
$_lang['collections.err.column_name_cant_change'] = 'U sloupce ID nelze změnit jeho název. Tento sloupec je vyžadován, pokud tento sloupec nechcete zobrazovat nastavte možnost <strong>Skrytý</strong> na <strong>Ano</strong>.';
$_lang['collections.err.template_remove_last'] = 'Nelze odebrat poslední zobrazení.';
$_lang['collections.err.template_remove_global'] = 'Výchozí zobrazení nelze odstranit. Prosím nastavte nejprve jiné zobrazení jako výchozí a tuto akci zopakujte.';
$_lang['collections.err.template_ae_name'] = 'Šablona s tímto názvem již existuje.';
$_lang['collections.err.column_dot_name'] = 'Název sloupce nemůže obsahovat tečku.';
$_lang['collections.err.template_ns_label'] = 'Není zadáno Označení.';
$_lang['collections.err.permanent_sort'] = 'Permanent sort can\'t contain an <strong>everytime</strong>, a <strong>[[+column]]</strong> or (if filled) <strong>no</strong> sort field condition to use drag & drop sorting.';
