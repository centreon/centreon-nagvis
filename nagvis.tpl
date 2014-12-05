{literal}
<style>
html, body {
  height: 100%;
}

.select-map {
  padding-bottom: 10px;
}

.map {
  width: 100%;
}
</style>
{/literal}
<div class="select-map">
<select name='view'>
{foreach from=$listmap item=map}
<option>{$map}</option>
{/foreach}
</select>
</div>

<iframe class="map" id="map" src="{$mapurl}" frameborder="0"></iframe>

<script type="text/javascript">
var nagvis_uri = "{$nagvis_uri}";

{literal}

function resizeMap() {
  var height = jQuery('html').height();
  jQuery('#map').height(0);

  /* Header */
  height = height - jQuery('#header').height() - jQuery('#forMenuAjax').height();

  /* Footer */
  height = height - jQuery('#footer').height() - 10;
  height = height - jQuery('#contener').height();
  jQuery('#map').height(height);
}

jQuery(function() {
  resizeMap();
  jQuery('.select-map > select').change(loadMap);
});

jQuery(window).resize(resizeMap);

function loadMap() {
  var mapname = jQuery('.select-map > select').val();
  var mapData = {
    mod: 'Map',
    context_menu: 0,
    hover_menu: 1,
    header_menu: 0,
    show: mapname
  };
  mapurl = nagvis_uri + '?' + jQuery.param(mapData);
  jQuery('#map').attr('src', mapurl);
}
</script>
{/literal}
