<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyAttribute;
use App\Models\AttributeGroup;
use Database\Seeders\AttributeSeeder;
use App\Models\PropertySitesContent;
use App\Models\PropertySync;
use App\Models\PropertySites;

class PropertyHtmlController extends Controller
{
	public function generateHtml($id)
	{
		// get property
		$property = Property::findOrFail($id);

		// get PropertySitesContent
		$propertySitesContent = PropertySitesContent::where('property_id', $id)->get();
		$propertySitesContent_array = $propertySitesContent->toArray();
		// search youtube urls and get video id for video player
		$youtube_ids = array();
		if (!empty($propertySitesContent_array)) {
			foreach ($propertySitesContent_array as $content) {
				$pos = strpos($content['content'], 'youtube.com/watch?v');
				if ($pos === false) {
					break;
				} else {
					$query_str = parse_url($content['content'], PHP_URL_QUERY);
					parse_str($query_str, $query_params);
					if (!empty($query_params['v'])) {
						$youtube_ids[] = $query_params['v'];
					}
				}
			}
		}

		// get PropertyAttribute
		$PropertyAttribute = PropertyAttribute::where('property_id', $id)->get();
		$PropertyAttribute_array = $PropertyAttribute->toArray();
		if (!empty($PropertyAttribute_array)) {
			$PropertyAttribute_sorted = array();
			foreach ($PropertyAttribute_array as $attr) {
				$PropertyAttribute_sorted[$attr['attribute_id']] = $attr['value'];
			}
		} else {
			$PropertyAttribute_sorted = array('', '', '', '', '', '', '', '', '', '', '', '', '');
		}

		$attributeGroups = AttributeGroup::with('attributes')->get();
		$allAmenitiesAttributes = json_decode($attributeGroups[0]['attributes'][0]['options'], true);
		$amenities = !empty($PropertyAttribute_sorted[1]) ? json_decode($PropertyAttribute_sorted[1], true) : '';
		$allKitchensAttributes = json_decode($attributeGroups[0]['attributes'][1]['options'], true);
		$kitchens = !empty($PropertyAttribute_sorted[2]) ? json_decode($PropertyAttribute_sorted[2], true) : '';

		$before_title = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill="#269" d="M33.398 23.678c-7.562 4.875-20.062-.438-18.375-8.062 1.479-6.684 9.419-4.763 11.225-3.861 1.806.902.713-3.889-3.475-5.327C17.1 4.48 10.156 4.893 7.961 14.678c-1.5 6.687 1.438 16.062 12.719 16.187 11.281.125 12.718-7.187 12.718-7.187z"/><path fill="#55ACEE" d="M35.988 25.193c0-2.146-2.754-2.334-4-1.119-2.994 2.919-7.402 4.012-13.298 2.861-10.25-2-10.341-14.014-3.333-17.441 3.791-1.854 8.289.341 9.999 1.655 1.488 1.143 4.334 2.66 4.185.752C29.223 7.839 21.262-.86 10.595 4.64-.071 10.14 0 22.553 0 24.803v7.25C0 34.262 1.814 36 4.023 36h28C34.232 36 36 34.262 36 32.053c0 0-.004-6.854-.012-6.86z"/></svg>';
		$after_title = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill="#C1694F" d="M21.978 20.424c-.054-.804-.137-1.582-.247-2.325-.133-.89-.299-1.728-.485-2.513-.171-.723-.356-1.397-.548-2.017-.288-.931-.584-1.738-.852-2.4-.527-1.299-.943-2.043-.943-2.043l-3.613.466s.417.87.868 2.575c.183.692.371 1.524.54 2.495.086.49.166 1.012.238 1.573.1.781.183 1.632.242 2.549.034.518.058 1.058.074 1.619.006.204.015.401.018.611.01.656-.036 1.323-.118 1.989-.074.6-.182 1.197-.311 1.789-.185.848-.413 1.681-.67 2.475-.208.643-.431 1.261-.655 1.84-.344.891-.69 1.692-.989 2.359-.502 1.119-.871 1.863-.871 2.018 0 .49.35 1.408 2.797 2.02 3.827.956 4.196-.621 4.196-.621s.243-.738.526-2.192c.14-.718.289-1.605.424-2.678.081-.642.156-1.348.222-2.116.068-.8.125-1.667.165-2.605.03-.71.047-1.47.055-2.259.002-.246.008-.484.008-.737 0-.64-.03-1.261-.071-1.872z"/><path fill="#D99E82" d="M18.306 30.068c-1.403-.244-2.298-.653-2.789-.959-.344.891-.69 1.692-.989 2.359.916.499 2.079.895 3.341 1.114.729.127 1.452.191 2.131.191.414 0 .803-.033 1.176-.08.14-.718.289-1.605.424-2.678-.444.157-1.548.357-3.294.053zm1.06-4.673c-1.093-.108-1.934-.348-2.525-.602-.185.848-.413 1.681-.67 2.475.864.326 1.881.561 2.945.666.429.042.855.064 1.27.064.502 0 .978-.039 1.435-.099.068-.8.125-1.667.165-2.605-.628.135-1.509.21-2.62.101zm.309-2.133c.822 0 1.63-.083 2.366-.228.002-.246.008-.484.008-.737 0-.641-.029-1.262-.071-1.873-.529.138-1.285.272-2.352.286-1.084-.005-1.847-.155-2.374-.306.006.204.015.401.018.611.01.656-.036 1.323-.118 1.989.763.161 1.605.253 2.461.257l.062.001zm-.249-4.577c.825-.119 1.59-.333 2.304-.585-.133-.89-.299-1.728-.485-2.513-.496.204-1.199.431-2.181.572-.91.132-1.605.124-2.129.077.1.781.183 1.632.242 2.549.152.006.29.029.446.029.588.001 1.2-.043 1.803-.129zm1.271-5.116c-.288-.931-.584-1.738-.852-2.4-.443.222-1.004.456-1.737.659-.795.221-1.437.309-1.951.339.183.692.371 1.524.54 2.495.681-.068 1.383-.179 2.094-.376.679-.188 1.31-.44 1.906-.717z"/><path fill="#3E721D" d="M32.61 4.305c-.044-.061-4.48-5.994-10.234-3.39-2.581 1.167-4.247 3.074-4.851 5.535-1.125-1.568-2.835-2.565-5.093-2.968C6.233 2.376 2.507 9.25 2.47 9.32c-.054.102-.031.229.056.305s.217.081.311.015c.028-.02 2.846-1.993 7.543-1.157 4.801.854 8.167 1.694 8.201 1.702.02.005.041.007.061.007.069 0 .136-.028.184-.08.032-.035 3.22-3.46 6.153-4.787 4.339-1.961 7.298-.659 7.326-.646.104.046.227.018.298-.07.072-.087.075-.213.007-.304z"/><path fill="#5C913B" d="M27.884 7.63c-4.405-2.328-7.849-1.193-9.995.22-2.575-.487-7.334-.459-11.364 4.707-4.983 6.387-.618 14.342-.573 14.422.067.119.193.191.327.191.015 0 .031-.001.046-.003.151-.019.276-.127.316-.274.015-.054 1.527-5.52 5.35-10.118 2.074-2.496 4.55-4.806 6.308-6.34 1.762.298 4.327.947 6.846 2.354 4.958 2.773 7.234 7.466 7.257 7.513.068.144.211.226.379.212.158-.018.289-.133.325-.287.02-.088 1.968-8.8-5.222-12.597z"/></svg>';

		$latitude = '35.1855659';
		$longitude = '23.6754646';
		if ($property->latitude) $latitude = $property->latitude;
		if ($property->longitude) $longitude = $property->longitude;
		$property_title = $property->title ? $property->title : '';

		$primaryImage = $property->getMedia('primary_image')->first();
		$property_external_primary_image = $primaryImage ? $primaryImage->getUrl() : '';

		$header_box_title_array = array();
		if ($property->city) $header_box_title_array[] = $property->city;
		if ($property->state_or_region) $header_box_title_array[] = $property->state_or_region;
		if ($property->country) $header_box_title_array[] = $property->country;
		$header_box_title = implode(', ', $header_box_title_array);

		$googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');

		$htmlContent = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link href=\"https://fonts.googleapis.com/css?family=Roboto:400,600,700\" rel=\"stylesheet\" type=\"text/css\"/>
		<link href=\"https://cdn.jsdelivr.net/gh/sachinchoolur/lightgallery.js@master/dist/css/lightgallery.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
                <title>{$property_title}</title>
            </head>
	    
	    <style>
		*, ::before, ::after {
		    --color-title: #1ebae2;
		    --color-headers: #a06957;
		    --color-text: #789ab2;
		    --color-check: #0000ff;
		}
		body {margin: 0px;font-family: \"Roboto\";font-weight: 400;text-transform: none;font-size: 17px;}
		.primary_image {
		    height: 500px;
		    background-image: url('{$property_external_primary_image}');
		    background-position: center center;
		    background-repeat: no-repeat;
		    background-size: cover;
		    transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
		    position: relative;
		}
		.youtube-icon-wrap {width: 50px;background: #fff;position: absolute;right: 200px;top: 100px;height: 50px;cursor:pointer;}
		.youtube-icon-wrap svg {margin-top: -20px;margin-left: -20px;}
		.photo-icon-wrap {width: 90px;background: transparent;position: absolute;right: 170px;top: 250px;height: 50px;cursor:pointer;}
		.photo-icon-wrap svg {fill: #1ebae2;}
		.header-box-title {color: #ececec;display: block;padding-top: 350px;text-align: center;font-weight: 600;}
		@media (max-width: 575px) {
		    .youtube-icon-wrap {right: 100px;}
		    .photo-icon-wrap {right: 85px;}
		    .header-box-title {padding-top: 400px;}
		}
		
		section.hero {max-width: 1140px;margin-right: auto;margin-left: auto;}
		section:not(.hero) {margin-top: 80px;max-width: 770px;margin-right: auto;margin-left: auto;}
		@media (max-width: 575px) { section {padding-left: 20px;padding-right: 20px;} }
		.emoji-35 {height: 35px;width: 35px;display: block;margin: auto 10px;}
		.emoji-17 {height: 17px;width: 17px;display: inline-block;margin-right: 5px;line-height: 22px;vertical-align: sub;}
		.fa-umbrella-beach-svg, .fa-plane-svg, .fa-city-svg, .fa-shopping-cart-svg, .fa-glass-martini-svg, .fa-anchor-svg,
		.fa-clinic-medical-svg, .fa-house-user-svg, .fa-school-svg, .fa-fort-awesome-svg, .fa-bed-svg, 
		.fa-bed-svg, .fa-bath-svg, .fa-user-friends-svg, .fa-layer-group-svg, .fa-swimming-pool-svg, .fa-dog-svg 
		    {height: 40px;width: 40px;display: block;font-size: 35px;fill: var(--color-headers);color: var(--color-headers);}
		.section_title {font-size: 24px;text-align: center;color: var(--color-headers);font-weight: 600;}
		.section_subtitle {font-size: 16px;text-align: center;color: var(--color-title);font-weight: 600;}
		
		.hero {transition: background .3s,border .3s,border-radius .3s,box-shadow .3s,transform .4s;}
		.hero > .title {font-size: 35px;text-align: center;color: var(--color-title);font-weight: 600;display: flex;flex-direction: row;justify-content: center;flex-wrap:wrap;}
		.hero > .desc {font-size: 17px;text-align: center;line-height: 1.5;color: var(--color-text);}
		
		.section_items {display: flex;border-style: solid;border-width: 1px 0px 0px 0px;
		    transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;padding: 3px 0px 3px 0px;}
		.section_items-item {width: 50%;background-color: var(--color-title);padding: 20px;display: flex;
		    text-align: start;flex-direction: row;}
		.section_items-item > .item_icon {margin-right: 15px;padding-top: 5px;}
		.section_items-item > .item_content {margin-right: 15px;color: var(--color-headers);}
		.item_content-name {font-weight: 700;line-height: 1.5;}
		@media (max-width: 575px) {
		    .section_items-item {flex-direction: column;text-align: center;}
		    .section_items-item > .item_content {margin: auto;}
		    .section_items-item > .item_icon {margin: auto;}
		}
		
		.details {border-bottom: 1px solid #d5d8dc;padding-bottom: 25px;}
		.details > .section_subtitle {color: var(--color-text);line-height: 1.5;}
		.details_button button {color: var(--color-headers);text-decoration: none;font-weight: 700;cursor:pointer;font-size: 16px;}
		.details_button > button >  .fa-caret-square-down-svg {padding-left: 3px;padding-right: 3px;width: 17px;
		    height: 17px;display: inline-block;vertical-align: sub;fill: var(--color-headers);}
		.details_items {padding: 15px;display: none;visibility: hidden;opacity: 0;transition: display 1s ease-in-out, visibility 1s ease-in-out, opacity 1s ease-in-out;}
		.details_items.active {display: block;visibility: visible;opacity: 1;transition: display 1s ease-in-out, visibility 1s ease-in-out, opacity 1s ease-in-out;}
		.details_items {color: var(--color-text);line-height: 1.5;}
		
		.check_items {padding: 10px;}
		.check_item {display: inline-block;color: var(--color-text);padding-left: 15px;line-height: 24px;}
		.check_item .fa-check-svg {vertical-align: middle;margin-right: 5px;fill: var(--color-check);width: 17px;height: 17px;
		    display: inline-block;}
		@media (max-width: 575px) { .check_items {padding: 10px 0px;} }
		
		.gallery_items {display: flex;flex-wrap: wrap;gap: 10px;}
		.lg-outer .lg-img-wrap {padding-top: 47px;}
		.gallery_item {flex: 0 1 calc(25% - 8px);height: 120px;overflow: hidden;cursor: pointer;}
		.gallery_item > img {width: 100%;height: 100%;object-fit: cover;-webkit-transition: -webkit-transform 0.15s ease 0s;
			    -moz-transition: -moz-transform 0.15s ease 0s;-o-transition: -o-transform 0.15s ease 0s;transition: transform 0.15s ease 0s;
			    -webkit-transform: scale3d(1, 1, 1);transform: scale3d(1, 1, 1);}
		.gallery_item > img:hover {-webkit-transform: scale3d(1.1, 1.1, 1.1);transform: scale3d(1.1, 1.1, 1.1);transition: transform ease-in-out .5s;}
		@media (max-width: 575px) { .gallery_items {flex-direction: column;} }
		
		#map {height: 500px;width: 100%;}
		
		footer {background: var(--color-text);text-align: center;color: #fff;margin-top: 100px;padding: 80px 0px;}
		.footer_title {font-size: 42px;line-height: 58px;margin-bottom: 20px;font-weight: 600;}
		.footer_subtitle {}

		/*modal video*/
		.modal-overlay {position: fixed; top: 0; left: 0; width: 100%; height: 100%;
		    background: rgba(0, 0, 0, 0.9); display: none; align-items: center; justify-content: center;}
		.modal-content {position: relative; width: 90vw; height: 90vh;
		    background: black; display: flex; align-items: center; justify-content: center;}
		.close-btn {position: absolute; top: 10px; right: 15px; color: white; font-size: 24px; cursor: pointer;
			background: rgba(0, 0, 0, 0.7); border-radius: 50%; padding: 5px 10px;}
		    #youtubePlayer { width: 100%; height: 100%; } 

	    </style>

	    <script>
		function initMap() {
		    var location = { lat: {$latitude}, lng: {$longitude} };
		    var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 12,
				center: location
		    });		    
		    // var marker = new google.maps.Marker({
			// 	position: location,
			// 	map: map
		    // });

			// Add circle around the marker
			var circle = new google.maps.Circle({
				map: map,
				center: location,
				radius: 500, // in meters
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 1,
				fillColor: '#FF0000',
				fillOpacity: 0.2
			});
			
			// Optional: fit map to show entire circle
	    	map.fitBounds(circle.getBounds());
		}
	    </script>
	    
            <body>";

		$header = '
                <header class="primary_image">
		    <a class="youtube-icon-wrap" id="openVideo" data-video-id="OkR2OsbBSU4" data-playlist="ZhJgLYB49Us,RsZTy8XFzEg">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="#eb0929" width="100px"><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>
		    </a>
		    <a class="photo-icon-wrap" id="openGallery">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M608 0H160a32 32 0 0 0 -32 32v96h160V64h192v320h128a32 32 0 0 0 32-32V32a32 32 0 0 0 -32-32zM232 103a9 9 0 0 1 -9 9h-30a9 9 0 0 1 -9-9V73a9 9 0 0 1 9-9h30a9 9 0 0 1 9 9zm352 208a9 9 0 0 1 -9 9h-30a9 9 0 0 1 -9-9v-30a9 9 0 0 1 9-9h30a9 9 0 0 1 9 9zm0-104a9 9 0 0 1 -9 9h-30a9 9 0 0 1 -9-9v-30a9 9 0 0 1 9-9h30a9 9 0 0 1 9 9zm0-104a9 9 0 0 1 -9 9h-30a9 9 0 0 1 -9-9V73a9 9 0 0 1 9-9h30a9 9 0 0 1 9 9zm-168 57H32a32 32 0 0 0 -32 32v288a32 32 0 0 0 32 32h384a32 32 0 0 0 32-32V192a32 32 0 0 0 -32-32zM96 224a32 32 0 1 1 -32 32 32 32 0 0 1 32-32zm288 224H64v-32l64-64 32 32 128-128 96 96z"/></svg>
		    </a>
		    <div class="header-box-title">' . $header_box_title . '</div>
		</header>';
		$htmlContent .= $header;

		$property_brief_description = $property->brief_description ? $property->brief_description : '';
		$hero = "<section class=\"hero\">	    
		<h1 class=\"title\">
		    <span class=\"emoji-35\">{$before_title}</span>
		    <span>{$property_title}</span>
		    <span class=\"emoji-35\">{$after_title}</span>
		</h1>
		<p class=\"desc\">{$property_brief_description}</p>
	    </section>";
		$htmlContent .= $hero;

		$fa_bed_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-bed-svg" viewBox="0 0 640 512"><path d="M176 256c44.1 0 80-35.9 80-80s-35.9-80-80-80-80 35.9-80 80 35.9 80 80 80zm352-128H304c-8.8 0-16 7.2-16 16v144H64V80c0-8.8-7.2-16-16-16H16C7.2 64 0 71.2 0 80v352c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-48h512v48c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V240c0-61.9-50.1-112-112-112z"/></svg>';
		$fa_bath_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-bath-svg" viewBox="0 0 512 512"><path d="M32 384a95.4 95.4 0 0 0 32 71.1V496a16 16 0 0 0 16 16h32a16 16 0 0 0 16-16V480H384v16a16 16 0 0 0 16 16h32a16 16 0 0 0 16-16V455.1A95.4 95.4 0 0 0 480 384V336H32zM496 256H80V69.3a21.3 21.3 0 0 1 36.3-15l19.3 19.3c-13.1 29.9-7.6 59.1 8.6 79.7l-.2 .2A16 16 0 0 0 144 176l11.3 11.3a16 16 0 0 0 22.6 0L283.3 81.9a16 16 0 0 0 0-22.6L272 48a16 16 0 0 0 -22.6 0l-.2 .2c-20.6-16.2-49.8-21.8-79.7-8.6L150.2 20.3A69.3 69.3 0 0 0 32 69.3V256H16A16 16 0 0 0 0 272v16a16 16 0 0 0 16 16H496a16 16 0 0 0 16-16V272A16 16 0 0 0 496 256z"/></svg>';
		$fa_user_friends_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-user-friends-svg" viewBox="0 0 640 512"><path d="M192 256c61.9 0 112-50.1 112-112S253.9 32 192 32 80 82.1 80 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C51.6 288 0 339.6 0 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zM480 256c53 0 96-43 96-96s-43-96-96-96-96 43-96 96 43 96 96 96zm48 32h-3.8c-13.9 4.8-28.6 8-44.2 8s-30.3-3.2-44.2-8H432c-20.4 0-39.2 5.9-55.7 15.4 24.4 26.3 39.7 61.2 39.7 99.8v38.4c0 2.2-.5 4.3-.6 6.4H592c26.5 0 48-21.5 48-48 0-61.9-50.1-112-112-112z"/></svg>';
		$fa_layer_group_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-layer-group-svg" viewBox="0 0 512 512"><path d="M12.4 148l232.9 105.7c6.8 3.1 14.5 3.1 21.3 0l232.9-105.7c16.6-7.5 16.6-32.5 0-40L266.7 2.3a25.6 25.6 0 0 0 -21.3 0L12.4 108c-16.6 7.5-16.6 32.5 0 40zm487.2 88.3l-58.1-26.3-161.6 73.3c-7.6 3.4-15.6 5.2-23.9 5.2s-16.3-1.7-23.9-5.2L70.5 210l-58.1 26.3c-16.6 7.5-16.6 32.5 0 40l232.9 105.6c6.8 3.1 14.5 3.1 21.3 0L499.6 276.3c16.6-7.5 16.6-32.5 0-40zm0 127.8l-57.9-26.2-161.9 73.4c-7.6 3.4-15.6 5.2-23.9 5.2s-16.3-1.7-23.9-5.2L70.3 337.9 12.4 364.1c-16.6 7.5-16.6 32.5 0 40l232.9 105.6c6.8 3.1 14.5 3.1 21.3 0L499.6 404.1c16.6-7.5 16.6-32.5 0-40z"/></svg>';
		$fa_swimming_pool_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-swimming-pool-svg" viewBox="0 0 640 512"><path d="M624 416h-16c-26 0-45.8-8.4-56.1-17.9-8.9-8.2-19.7-14.1-31.8-14.1h-16.3c-12.1 0-22.9 5.9-31.8 14.1C461.8 407.6 442 416 416 416s-45.8-8.4-56.1-17.9c-8.9-8.2-19.7-14.1-31.8-14.1h-16.3c-12.1 0-22.9 5.9-31.8 14.1C269.8 407.6 250 416 224 416s-45.8-8.4-56.1-17.9c-8.9-8.2-19.7-14.1-31.8-14.1h-16.3c-12.1 0-22.9 5.9-31.8 14.1C77.8 407.6 58 416 32 416H16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h16c38.6 0 72.7-12.2 96-31.8 23.3 19.7 57.4 31.8 96 31.8s72.7-12.2 96-31.8c23.3 19.7 57.4 31.8 96 31.8s72.7-12.2 96-31.8c23.3 19.7 57.4 31.8 96 31.8h16c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm-400-32v-96h192v96c19.1 0 30.9-6.2 34.4-9.4 9.2-8.5 19.2-14.3 29.6-18.1V128c0-17.6 14.4-32 32-32s32 14.4 32 32v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16c0-52.9-43.1-96-96-96s-96 43.1-96 96v96H224v-96c0-17.6 14.4-32 32-32s32 14.4 32 32v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16c0-52.9-43.1-96-96-96s-96 43.1-96 96v228.5c10.4 3.7 20.4 9.6 29.6 18.1 3.5 3.3 15.3 9.4 34.4 9.4z"/></svg>';
		$fa_dog_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-dog-svg" viewBox="0 0 576 512"><path d="M298.1 224 448 277.6V496a16 16 0 0 1 -16 16H368a16 16 0 0 1 -16-16V384H192V496a16 16 0 0 1 -16 16H112a16 16 0 0 1 -16-16V282.1C58.8 268.8 32 233.7 32 192a32 32 0 0 1 64 0 32.1 32.1 0 0 0 32 32zM544 112v32a64 64 0 0 1 -64 64H448v35.6L320 197.9V48c0-14.3 17.2-21.4 27.3-11.3L374.6 64h53.6c10.9 0 23.8 7.9 28.6 17.7L464 96h64A16 16 0 0 1 544 112zm-112 0a16 16 0 1 0 -16 16A16 16 0 0 0 432 112z"/></svg>';
		$fa_umbrella_beach_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-umbrella-beach-svg" viewBox="0 0 640 512"><path d="M115.4 136.9l102.1 37.2c35.2-81.5 86.2-144.3 139-173.7-95.9-4.9-188.8 37-248.5 111.8-6.7 8.4-2.7 21.1 7.4 24.7zm132.3 48.2l238.5 86.8c35.8-121.4 18.7-231.7-42.6-254-7.4-2.7-15.1-4-23.1-4-58 0-128.3 69.2-172.8 171.2zM521.5 60.5c6.2 16.3 10.8 34.6 13.2 55.2 5.7 49.9-1.4 108.2-19 167l102.6 37.4c10.1 3.7 21.3-3.4 21.6-14.2 2.3-95.7-41.9-187.4-118.4-245.4zM560 448H321.1L386 269.5l-60.1-21.9-72.9 200.4H16c-8.8 0-16 7.2-16 16v32C0 504.8 7.2 512 16 512h544c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16z"/></svg>';
		$fa_city_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-city-svg" viewBox="0 0 640 512"><path d="M616 192H480V24c0-13.3-10.7-24-24-24H312c-13.3 0-24 10.7-24 24v72h-64V16c0-8.8-7.2-16-16-16h-16c-8.8 0-16 7.2-16 16v80h-64V16c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16v80H24c-13.3 0-24 10.7-24 24v360c0 17.7 14.3 32 32 32h576c17.7 0 32-14.3 32-32V216c0-13.3-10.8-24-24-24zM128 404c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm128 192c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm160 96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm160 288c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40zm0-96c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40z"/></svg>';
		$fa_plane_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-plane-svg" viewBox="0 0 576 512"><path d="M480 192H365.7L260.6 8.1A16 16 0 0 0 246.7 0h-65.5c-10.6 0-18.3 10.2-15.4 20.4L214.9 192H112l-43.2-57.6c-3-4-7.8-6.4-12.8-6.4H16C5.6 128-2 137.8 .5 147.9L32 256 .5 364.1C-2 374.2 5.6 384 16 384H56c5 0 9.8-2.4 12.8-6.4L112 320h102.9l-49 171.6c-2.9 10.2 4.8 20.4 15.4 20.4h65.5c5.7 0 11-3.1 13.9-8.1L365.7 320H480c35.4 0 96-28.7 96-64s-60.7-64-96-64z"/></svg>';
		$fa_shopping_cart_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-shopping-cart-svg" viewBox="0 0 576 512"><path d="M528.1 301.3l47.3-208C578.8 78.3 567.4 64 552 64H159.2l-9.2-44.8C147.8 8 137.9 0 126.5 0H24C10.7 0 0 10.7 0 24v16c0 13.3 10.7 24 24 24h69.9l70.2 343.4C147.3 417.1 136 435.2 136 456c0 30.9 25.1 56 56 56s56-25.1 56-56c0-15.7-6.4-29.8-16.8-40h209.6C430.4 426.2 424 440.3 424 456c0 30.9 25.1 56 56 56s56-25.1 56-56c0-22.2-12.9-41.3-31.6-50.4l5.5-24.3c3.4-15-8-29.3-23.4-29.3H218.1l-6.5-32h293.1c11.2 0 20.9-7.8 23.4-18.7z"/></svg>';
		$fa_glass_martini_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-glass-martini-svg" viewBox="0 0 512 512"><path d="M502.1 57.6C523.3 36.3 508.3 0 478.2 0H33.8C3.8 0-11.3 36.3 10 57.6L224 271.6V464h-56c-22.1 0-40 17.9-40 40 0 4.4 3.6 8 8 8h240c4.4 0 8-3.6 8-8 0-22.1-17.9-40-40-40h-56V271.6L502.1 57.6z"/></svg>';
		$fa_anchor_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-anchor-svg" viewBox="0 0 576 512"><path d="M13 352h32.4C67.2 454.7 181.9 512 288 512c106.2 0 220.9-57.4 242.6-160h32.4c10.7 0 16-12.9 8.5-20.5l-67-67c-4.7-4.7-12.3-4.7-17 0l-67 67c-7.6 7.6-2.2 20.5 8.5 20.5h35.1c-20.3 54.3-85 86.6-144.1 94V256h52c6.6 0 12-5.4 12-12v-40c0-6.6-5.4-12-12-12h-52v-5.5c37.3-13.2 64-48.7 64-90.5C384 43.8 341.6 .7 289.4 0 235.7-.7 192 42.5 192 96c0 41.8 26.7 77.4 64 90.5V192h-52c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h52v190c-58.9-7.4-123.8-39.7-144.1-94h35.1c10.7 0 16-12.9 8.5-20.5l-67-67c-4.7-4.7-12.3-4.7-17 0L4.5 331.5C-3.1 339.1 2.3 352 13 352zM288 64c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32z"/></svg>';
		$fa_clinic_medical_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-clinic-medical-svg" viewBox="0 0 576 512"><path d="M288 115L69.5 307.7c-1.6 1.5-3.7 2.1-5.5 3.4V496a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V311.1c-1.7-1.2-3.7-1.8-5.3-3.2zm96 261a8 8 0 0 1 -8 8h-56v56a8 8 0 0 1 -8 8h-48a8 8 0 0 1 -8-8v-56h-56a8 8 0 0 1 -8-8v-48a8 8 0 0 1 8-8h56v-56a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v56h56a8 8 0 0 1 8 8zm186.7-139.7l-255.9-226a39.9 39.9 0 0 0 -53.5 0l-256 226a16 16 0 0 0 -1.2 22.6L25.5 282.7a16 16 0 0 0 22.6 1.2L277.4 81.6a16 16 0 0 1 21.2 0L527.9 283.9a16 16 0 0 0 22.6-1.2l21.4-23.8a16 16 0 0 0 -1.2-22.6z"/></svg>';
		$fa_house_user_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-house-user-svg" viewBox="0 0 576 512"><path d="M570.7 236.3 512 184.4V48a16 16 0 0 0 -16-16H432a16 16 0 0 0 -16 16V99.7L314.8 10.3C308.5 4.6 296.5 0 288 0s-20.5 4.6-26.7 10.3l-256 226A18.3 18.3 0 0 0 0 248.2a18.6 18.6 0 0 0 4.1 10.7L25.5 282.7a21.1 21.1 0 0 0 12 5.3 21.7 21.7 0 0 0 10.7-4.1l15.9-14V480a32 32 0 0 0 32 32H480a32 32 0 0 0 32-32V269.9l15.9 14A21.9 21.9 0 0 0 538.6 288a20.9 20.9 0 0 0 11.9-5.3l21.4-23.8A21.6 21.6 0 0 0 576 248.2 21 21 0 0 0 570.7 236.3zM288 176a64 64 0 1 1 -64 64A64 64 0 0 1 288 176zM400 448H176a16 16 0 0 1 -16-16 96 96 0 0 1 96-96h64a96 96 0 0 1 96 96A16 16 0 0 1 400 448z"/></svg>';
		$fa_school_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-school-svg" viewBox="0 0 640 512"><path d="M0 224v272c0 8.8 7.2 16 16 16h80V192H32c-17.7 0-32 14.3-32 32zm360-48h-24v-40c0-4.4-3.6-8-8-8h-16c-4.4 0-8 3.6-8 8v64c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8v-16c0-4.4-3.6-8-8-8zm137.8-64l-160-106.7a32 32 0 0 0 -35.5 0l-160 106.7A32 32 0 0 0 128 138.7V512h128V368c0-8.8 7.2-16 16-16h96c8.8 0 16 7.2 16 16v144h128V138.7c0-10.7-5.4-20.7-14.3-26.6zM320 256c-44.2 0-80-35.8-80-80s35.8-80 80-80 80 35.8 80 80-35.8 80-80 80zm288-64h-64v320h80c8.8 0 16-7.2 16-16V224c0-17.7-14.3-32-32-32z"/></svg>';
		$fa_fort_awesome_svg = '<svg xmlns="http://www.w3.org/2000/svg" class="fa-fort-awesome-svg" viewBox="0 0 512 512"><path d="M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6zm-288.1-4.5c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64zm146.4 0c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64z"/></svg>';

		$property_pets = ($property->pets === 1) ? 'Yes' : 'No';
		$property_floors_of_property = $property->floors_of_property ? $property->floors_of_property : 'none';
		$property_max_guests = $property->max_guests ? $property->max_guests : 'none';

		$discover_home = "<section class=\"distances\">
		<h3 class=\"section_title\">Discover Your Vacation Home</h3>
		<p class=\"section_subtitle\">Your Ideal Getaway Awaits!</p>
		
		<div class=\"section_items\">
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_bed_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Bedrooms</div>
			    <div class=\"item_content-value\">2</div>
			</div>
		    </div>
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_bath_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Bathrooms</div>
			    <div class=\"item_content-value\">2</div>
			</div>
		    </div>
		</div>
		    
		<div class=\"section_items\">
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_user_friends_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Max Guests</div>
			    <div class=\"item_content-value\">{$property_max_guests}</div>
			</div>
		    </div>
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_layer_group_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Floors</div>
			    <div class=\"item_content-value\">{$property_floors_of_property}</div>
			</div>
		    </div>
		</div>
		    
		<div class=\"section_items\">
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_swimming_pool_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Pool</div>
			    <div class=\"item_content-value\">No</div>
			</div>
		    </div>
		    <div class=\"section_items-item\">
			<div class=\"item_icon\">{$fa_dog_svg}</div>
			<div class=\"item_content\">
			    <div class=\"item_content-name\">Pets Frendly</div>
			    <div class=\"item_content-value\">{$property_pets}</div>
			</div>
		    </div>		
		</div>
		
	    </section>";
		$htmlContent .= $discover_home;

		$fa_caret_square_down_svg = '<svg xmlns="http://www.w3.org/2000/svg"class="fa-caret-square-down-svg" viewBox="0 0 448 512"><path d="M448 80v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48h352c26.5 0 48 21.5 48 48zM92.5 220.5l123 123c4.7 4.7 12.3 4.7 17 0l123-123c7.6-7.6 2.2-20.5-8.5-20.5H101c-10.7 0-16.1 12.9-8.5 20.5z"/></svg>';

		$property_short_summary = $property->short_summary ? $property->short_summary : '';
		$property_description = $property->description ? $property->description : '';
		$details = "<section class=\"details\">
		<h3 class=\"section_title\">Vacation Property Details</h3>
		<p class=\"section_subtitle\">{$property_short_summary}</p>
		
		<div class=\"details_button\"><button id=\"details_button\">{$fa_caret_square_down_svg}Detail Description</button></div>
		<div class=\"details_items\"  id=\"details_items\">{$property_description}</div>
	    </section>";
		$htmlContent .= $details;

		$fa_check_svg = '<svg xmlns="http://www.w3.org/2000/svg"  class="fa-check-svg" viewBox="0 0 512 512"><path d="M173.9 439.4l-166.4-166.4c-10-10-10-26.2 0-36.2l36.2-36.2c10-10 26.2-10 36.2 0L192 312.7 432.1 72.6c10-10 26.2-10 36.2 0l36.2 36.2c10 10 10 26.2 0 36.2l-294.4 294.4c-10 10-26.2 10-36.2 0z"/></svg>';
		$check_items = '';
		if ($amenities) {
			foreach ($amenities as $attr) {
				$check_items .= '<div class="check_item">' . $fa_check_svg . $allAmenitiesAttributes[$attr] . '</div>';
			}
		}
		$check_items_kitchens = '';
		if ($kitchens) {
			foreach ($kitchens as $attr) {
				$check_items_kitchens .= '<div class="check_item">' . $fa_check_svg . $allKitchensAttributes[$attr] . '</div>';
			}
		}

		$htmlContent .= "<section class=\"facilities\">
		<h3 class=\"section_title\">Facilities and Amenities</h3>
		<p class=\"section_subtitle\">Your Ideal Getaway Awaits!</p>
		
		<div class=\"check_items\">{$check_items}</div>
		<div class=\"check_items\">{$check_items_kitchens}</div>
	    </section>";

		function getAttributeValue($attributes, $index, $default = 'N/A')
		{
			return isset($attributes[$index]) ? $attributes[$index] : $default;
		}

		$distances = "<section class=\"distances\">
			<h3 class=\"section_title\">Your Vacation Home Just a Click Away!</h3>
			<p class=\"section_subtitle\">Distance to (km)</p>
		
			<div class=\"section_items\">
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_umbrella_beach_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Beach (m)</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 3) . "</div>
					</div>
				</div>
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_city_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Infrastructure</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 4) . "</div>
					</div>
				</div>
			</div>
		
			<div class=\"section_items\">
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_plane_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Airport (km)</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 5) . "</div>
					</div>
				</div>
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_shopping_cart_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Supermarket</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 6) . "</div>
					</div>
				</div>
			</div>
		
			<div class=\"section_items\">
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_glass_martini_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Restaurant</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 7) . "</div>
					</div>
				</div>
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_anchor_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Marina</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 8) . "</div>
					</div>
				</div>        
			</div>
		
			<div class=\"section_items\">
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_clinic_medical_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Medical office</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 9) . "</div>
					</div>
				</div>
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_house_user_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Police office</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 10) . "</div>
					</div>
				</div>        
			</div>
		
			<div class=\"section_items\">
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_school_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">School</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 11) . "</div>
					</div>
				</div>
				<div class=\"section_items-item\">
					<div class=\"item_icon\">{$fa_fort_awesome_svg}</div>
					<div class=\"item_content\">
						<div class=\"item_content-name\">Entertainment facility</div>
						<div class=\"item_content-value\">" . getAttributeValue($PropertyAttribute_sorted, 12) . "</div>
					</div>
				</div>        
			</div>
		
		</section>";
		$htmlContent .= $distances;

		$gallery_items = '';
		if (!empty($property->getMedia('gallery'))) {
			foreach ($property->getMedia('gallery') as $media) {
				$gallery_items .= '<div class="gallery_item" data-src="' . $media->getUrl() . '" data-pinterest-text="Pinterest share text" data-tweet-text="Tweet share text">
			    <img class="img-responsive" src="' . $media->getUrl('thumb') . '" alt="Media Alt">
		    </div>';
			}

			$htmlContent .= "<section class=\"gallery\">
		    <h3 class=\"section_title\">Visualize Your Dream Vacation</h3>
		    <p class=\"section_subtitle\">Our Photo Gallery</p>
		    
		    <div id=\"lightgallery\" class=\"list-unstyled row gallery_items\">{$gallery_items}</div>
		</section>";
		}

		$htmlContent .= "<section class=\"location\">
		<h3 class=\"section_title\">Escape to Paradise</h3>
		<p class=\"section_subtitle\">Discover Our Stunning Location</p>
		
		<div id=\"map\"></div>
	    </section>
	    
	    <footer>
		<div class=\"footer_title\">Ready to Rent?</div>
		<div class=\"footer_subtitle\">Book Your Dream Vacation Today: Reserve Your Stay Now!</div>
	    </footer>";

		// Show-Hide details
		$htmlContent .= "<script>
		let detailsButton = document.getElementById(\"details_button\");
		let caretSquareDownSvg = document.querySelector(\"#details_button .fa-caret-square-down-svg\");
		let detailsItems = document.getElementById(\"details_items\");
		detailsButton.addEventListener(\"click\", function (e) {
		    e.stopPropagation();
		    if (detailsItems.classList.contains(\"active\")) {			
			detailsItems.classList.remove(\"active\");
			detailsButton.style.color = '#a06957';
			caretSquareDownSvg.style.fill = '#a06957';
			caretSquareDownSvg.style.transform = \"rotate(0deg)\";
		    } else {			
			detailsItems.classList.add(\"active\");
			detailsButton.style.color = '#1ebae2';
			caretSquareDownSvg.style.fill = '#1ebae2';
			caretSquareDownSvg.style.transform = \"rotate(180deg)\";
		    }
		}, false);
	    </script>";

		// YouTube Modal Window
		$youtube_ids = json_encode($youtube_ids);
		$htmlContent .= "<div id=\"videoModal\" class=\"modal-overlay\">
		<div class=\"modal-content\">
		    <span class=\"close-btn\">&times;</span>
		    <div id=\"youtubePlayer\"></div>
		</div>
	    </div>

	    <script src=\"https://www.youtube.com/iframe_api\"></script>
	    <script>
		let player;
		let modal = document.getElementById(\"videoModal\");
		let closeBtn = document.querySelector(\".close-btn\");
		let openVideo = document.getElementById(\"openVideo\");

		function onYouTubeIframeAPIReady() {
		    player = new YT.Player('youtubePlayer', {
			height: '100%',
			width: '100%',
			playerVars: { 'autoplay': 1, 'controls': 1 }
		    });
		}

		openVideo.addEventListener(\"click\", function(e) {
		    e.preventDefault();
		    let videoId = openVideo.getAttribute(\"data-video-id\");
		    let playlist = openVideo.getAttribute(\"data-playlist\").split(',');

		    player.loadPlaylist({
			playlist: " . $youtube_ids . ",
			index: 0,
			startSeconds: 0
		    });

		    modal.style.display = \"flex\";
		});

		closeBtn.addEventListener(\"click\", function() {
		    modal.style.display = \"none\";
		    player.stopVideo();
		});

		window.addEventListener(\"click\", function(e) {
		    if (e.target === modal) {
			modal.style.display = \"none\";
			player.stopVideo();
		    }
		});
	    </script>
	    
	    <script src=\"https://cdn.rawgit.com/sachinchoolur/lightgallery.js/master/dist/js/lightgallery.js\"></script>
	    <script src=\"https://cdn.rawgit.com/sachinchoolur/lg-fullscreen.js/master/dist/lg-fullscreen.js\"></script>
	    <script src=\"https://cdn.rawgit.com/sachinchoolur/lg-zoom.js/master/dist/lg-zoom.js\"></script>
	    <script src=\"https://cdn.rawgit.com/sachinchoolur/lg-share.js/master/dist/lg-share.js\"></script>
	    <script>
		let openGallery = document.getElementById(\"openGallery\");
		openGallery.addEventListener(\"click\", function(e) {
			document.querySelector(\".img-responsive\").click();
		    });
		lightGallery(document.getElementById('lightgallery'), { download: false,});
	    </script>
		
	    <script async defer 
		src=\"https://maps.googleapis.com/maps/api/js?key={$googleMapsApiKey}&callback=initMap\">
	    </script>
		
            </body>
            </html>
        ";

		//  return response($htmlContent)
		//    ->header('Content-Type', 'text/html');
		// exit;
		// hash
		$siteRecord = PropertySites::where('site', 'Presentation')->first();
		$url = PropertySync::where([
			'synchronization_id' => $siteRecord->id,
			'property_id' => $property->id,
		])->value('url');

		if (!empty($url)) {
			$url = parse_url($url, PHP_URL_PATH);
			$url = trim($url, '/');
			$url = explode('/', $url);
			$hash = $url[1];
		} else {
			$hash = Str::random(16);
		}

		$presentationSettingsUrl = $siteRecord ? $siteRecord->url : null;
		$parsedUrl = parse_url($presentationSettingsUrl);

		if (isset($parsedUrl['host'])) {
			$domain = preg_replace('#^www\.#', '', $parsedUrl['host']);
		} else {
			return;
		}

		$currentDocumentRoot = $_SERVER['DOCUMENT_ROOT'];
		$currentDomain = $_SERVER['HTTP_HOST'];
		$newDocumentRoot = str_replace($currentDomain, $domain, $currentDocumentRoot);

		$path = strstr($newDocumentRoot, "/public_html", true) . "/public_html/properties/{$property->id}_{$hash}";

		$directoryPath = dirname($path);

		if (!is_dir($directoryPath)) {
			mkdir($directoryPath, 0755, true);
		}

		// create path
		//$path = public_path("properties/{$hash}");
		file_put_contents($path, $htmlContent);

		// create URL
		//$url = asset("properties/{$hash}");
		$url = "https://" . $domain . "/properties/{$property->id}_{$hash}";

		if ($siteRecord) {
			$syncId = $siteRecord->id;
		} else {
			// Если запись не найдена, можно задать дефолтное значение или обработать ошибку
			return;
		}

		// update DB
		$propertySync = PropertySync::firstOrNew([
			'synchronization_id'  => $syncId,
			'property_id'         => $property->id,
		]);

		// dd($siteRecord, $presentationSettingsUrl, $parsedUrl, $domain, $syncId, $url, $propertySync);
		$propertySync->url = $url;
		$propertySync->save();

		// $property->url_for_site_presentation = $url;
		// $property->save();
	}

	public function generateHtmlRealEstate($id)
	{
		// get property
		$property = Property::with([
			'propertyType',
			'bedrooms',
			'bathrooms',
			'kitchens',
			'other_rooms',
			'property_attributes'
		])->findOrFail($id);

		// get PropertySitesContent
		$propertySitesContent = PropertySitesContent::where('property_id', $id)->get();
		$propertySitesContent_array = $propertySitesContent->toArray();
		// search youtube urls and get video id for video player
		$youtube_ids = array();
		if (!empty($propertySitesContent_array)) {
			foreach ($propertySitesContent_array as $content) {
				$pos = strpos($content['content'], 'youtube.com/watch?v');
				if ($pos === false) {
					break;
				} else {
					$query_str = parse_url($content['content'], PHP_URL_QUERY);
					parse_str($query_str, $query_params);
					if (!empty($query_params['v'])) {
						$youtube_ids[] = $query_params['v'];
					}
				}
			}
		}

		$attributeGroups = AttributeGroup::with('attributes')->get();
		$allAmenitiesAttributes = json_decode($attributeGroups[0]['attributes'][0]['options'], true);
		$allKitchensAttributes = json_decode($attributeGroups[0]['attributes'][1]['options'], true);

		$latitude = '35.1855659';
		$longitude = '23.6754646';
		if ($property->latitude) $latitude = $property->latitude;
		if ($property->longitude) $longitude = $property->longitude;

		$property_title = $property->title ? $property->title : '';
		$description_title = $property->headline ? $property->headline : '';
		$description_summary = $property->short_summary ? $property->short_summary : '';
		$description = $property->description ? $property->description : '';
		$propertyType = $property->propertyType->name ? $property->propertyType->name : '';
		$bedrooms = $property->bedrooms ? count($property->bedrooms) : 0;
		$bathrooms = $property->bathrooms ? count($property->bathrooms) : 0;

		$attributes = array();
		if (!empty($property->property_attributes)) {
			foreach ($property->property_attributes as $data) {
				if ($data->attribute) {
					$attributes[$data->attribute->name] = $data->value;
				}
			}
		}

		// Amenities
		$attributesAmenities = isset($attributes['Amenities']) ? json_decode($attributes['Amenities']) : [];
		$amenities = [];
		if (!empty($attributesAmenities)) {
			foreach ($attributesAmenities as $value) {
				$amenities[] = $allAmenitiesAttributes[$value];
			}
		}

		// Kitchens
		$attributesKitchens = isset($attributes['Kitchens']) ? json_decode($attributes['Kitchens']) : [];
		$kitchens = [];
		if (!empty($attributesKitchens)) {
			foreach ($attributesKitchens as $value) {
				$kitchens[] = $allKitchensAttributes[$value];
			}
		}

		// primary image
		$primaryImage = $property->getMedia('primary_image')->first();
		$property_external_primary_image = $primaryImage ? $primaryImage->getUrl() : '';

		// floor plan image
		$finalFloorPlanImages = [];
		$floorPlanImages = $property->getMedia('floor-plan-list-gallery');
		if(!empty($floorPlanImages)) {
			foreach ($floorPlanImages as $image) {
				$finalFloorPlanImages[] = $image->getUrl();
			}
		} 

		// gallery image
		$finalGalleryImages = [];
		$galleryImages = $property->getMedia('gallery');
		if(!empty($galleryImages)) {
			foreach ($galleryImages as $image) {
				$finalGalleryImages[] = $image->getUrl();
			}
		} 

		// address
		$header_box_title_array = array();
		if ($property->city) $header_box_title_array[] = $property->city;
		if ($property->state_or_region) $header_box_title_array[] = $property->state_or_region;
		if ($property->country) $header_box_title_array[] = $property->country;
		$header_box_title = implode(', ', $header_box_title_array);

		$googleMapsApiKey = env('GOOGLE_MAPS_API_KEY');

		// dd($allAmenitiesAttributes);
		// dd(in_array('Sea view', $amenities));
		$data = [
			'property_title' => $property_title,
			'property_external_primary_image' => $property_external_primary_image,
			'description_title' => $description_title,
			'description_summary' => $description_summary,
			'description' => $description,
			'propertyType' => $propertyType,
			'bedrooms' => $bedrooms,
			'bathrooms' => $bathrooms,
			'yearOfConstruction' => $property->year_of_construction ? $property->year_of_construction : 'N/A',
			'year_of_renovation' => $property->year_of_renovation ? $property->year_of_renovation : 'N/A',
			'heating_system' => $property->heating_features ?  ucwords(str_replace('_', ' ', $property->heating_features)) : 'N/A',
			'suitable_for' => is_array($property->suitable_for) ? implode(', ',array_map(function ($key) {
				return ucwords(str_replace('_', ' ', $key));
			}, $property->suitable_for)) : 'N/A',
			'additiona_features' => is_array($property->aditional_features) ? implode(', ',array_map(function ($key) {
				return ucwords(str_replace('_', ' ', $key));
			}, $property->aditional_features)) : 'N/A',
			'roi' => $property->return_on_investment ? $property->return_on_investment : '',
			'sale_price_sqm' => $property->price_for_sale_per_sq_m ? $property->price_for_sale_per_sq_m : '',
			'sale_price' => $property->price_for_sale_eur ? $property->price_for_sale_eur : '',
			'monthly_rates' => $property->monthly_rate ? $property->monthly_rate : '',
			'monthly_rates_sqm' => $property->monthly_rate_sqm ? $property->monthly_rate_sqm : '',
			'floorspace' => $property->floorspace ? $property->floorspace : 'N/A',
			'floorspace_units' => $property->floorspace_units ? $property->floorspace_units : 'N/A',
			'see_view' => in_array('Sea view', $amenities) ? 'Yes' : 'No',
			'amenities' => implode(', ', $amenities),
			'kitchens' => implode(', ', $kitchens),
			'beach_distance' => $attributes['Beach distance'] ?? '',
			'infrastructure_distance' => $attributes['Infrastructure distance'] ?? '',
			'airport_distance' => $attributes['Airport distance'] ?? '',
			'supermarket_distance' => $attributes['Supermarket distance'] ?? '',
			'restaurant_distance' => $attributes['Restaurant distance'] ?? '',
			'school_distance' => $attributes['School distance'] ?? '',
			'marina_distance' => $attributes['Marina distance'] ?? '',
			'medical_office_distance' => $attributes['Medical office distance'] ?? '',
			'police_office_distance' => $attributes['Police office distance'] ?? '',
			'entertainment_facility_distance' => $attributes['Entertainment facility distance'] ?? '',
			'finalFloorPlanImages' => $finalFloorPlanImages,
			'galleryImages' => $finalGalleryImages,
			'address' => $header_box_title,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'googleMapsApiKey' => $googleMapsApiKey,
		];
		// dd($data);
		$htmlContent = view('templates.real-eastate-property', $data)->render();

		// return response($htmlContent)
		// 	->header('Content-Type', 'text/html');
		// exit;
		// 
		$siteRecord = PropertySites::where('site', 'Real Estate presentation')->first();
		$url = PropertySync::where([
			'synchronization_id' => $siteRecord->id,
			'property_id' => $property->id,
		])->value('url');

		if (!empty($url)) {
			$url = parse_url($url, PHP_URL_PATH);
			$url = trim($url, '/');
			$url = explode('/', $url);
			$hash = $url[1];
		} else {
			$hash = Str::random(16);
		}

		$presentationSettingsUrl = $siteRecord ? $siteRecord->url : null;
		$parsedUrl = parse_url($presentationSettingsUrl);

		if (isset($parsedUrl['host'])) {
			$domain = preg_replace('#^www\.#', '', $parsedUrl['host']);
		} else {
			return;
		}

		$currentDocumentRoot = $_SERVER['DOCUMENT_ROOT'];
		$currentDomain = $_SERVER['HTTP_HOST'];
		$newDocumentRoot = str_replace($currentDomain, $domain, $currentDocumentRoot);

		$path = strstr($newDocumentRoot, "/public_html", true) . "/public_html/properties/{$property->id}_{$hash}";

		$directoryPath = dirname($path);

		if (!is_dir($directoryPath)) {
			mkdir($directoryPath, 0755, true);
		}

		// create path
		//$path = public_path("properties/{$hash}");
		file_put_contents($path, $htmlContent);

		// create URL
		//$url = asset("properties/{$hash}");
		$url = "https://" . $domain . "/properties/{$property->id}_{$hash}";

		if ($siteRecord) {
			$syncId = $siteRecord->id;
		} else {
			// Если запись не найдена, можно задать дефолтное значение или обработать ошибку
			return;
		}

		// update DB
		$propertySync = PropertySync::firstOrNew([
			'synchronization_id'  => $syncId,
			'property_id'         => $property->id,
		]);

		// dd($siteRecord, $presentationSettingsUrl, $parsedUrl, $domain, $syncId, $url, $propertySync);
		$propertySync->url = $url;
		$propertySync->save();

		// $property->url_for_site_presentation = $url;
		// $property->save();
	}
}
