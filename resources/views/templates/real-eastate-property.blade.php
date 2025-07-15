<!DOCTYPE html>
<html lang="en-US">

<head>
   <meta charset="UTF-8">
   <script>
      var gform;
      gform || (document.addEventListener("gform_main_scripts_loaded", function() {
         gform.scriptsLoaded = !0
      }), window.addEventListener("DOMContentLoaded", function() {
         gform.domLoaded = !0
      }), gform = {
         domLoaded: !1,
         scriptsLoaded: !1,
         initializeOnLoaded: function(o) {
            gform.domLoaded && gform.scriptsLoaded ? o() : !gform.domLoaded && gform.scriptsLoaded ? window.addEventListener("DOMContentLoaded", o) : document.addEventListener("gform_main_scripts_loaded", o)
         },
         hooks: {
            action: {},
            filter: {}
         },
         addAction: function(o, n, r, t) {
            gform.addHook("action", o, n, r, t)
         },
         addFilter: function(o, n, r, t) {
            gform.addHook("filter", o, n, r, t)
         },
         doAction: function(o) {
            gform.doHook("action", o, arguments)
         },
         applyFilters: function(o) {
            return gform.doHook("filter", o, arguments)
         },
         removeAction: function(o, n) {
            gform.removeHook("action", o, n)
         },
         removeFilter: function(o, n, r) {
            gform.removeHook("filter", o, n, r)
         },
         addHook: function(o, n, r, t, i) {
            null == gform.hooks[o][n] && (gform.hooks[o][n] = []);
            var e = gform.hooks[o][n];
            null == i && (i = n + "_" + e.length), gform.hooks[o][n].push({
               tag: i,
               callable: r,
               priority: t = null == t ? 10 : t
            })
         },
         doHook: function(n, o, r) {
            var t;
            if (r = Array.prototype.slice.call(r, 1), null != gform.hooks[n][o] && ((o = gform.hooks[n][o]).sort(function(o, n) {
                  return o.priority - n.priority
               }), o.forEach(function(o) {
                  "function" != typeof(t = o.callable) && (t = window[t]), "action" == n ? t.apply(null, r) : r[0] = t.apply(null, r)
               })), "filter" == n) return r[0]
         },
         removeHook: function(o, n, t, i) {
            var r;
            null != gform.hooks[o][n] && (r = (r = gform.hooks[o][n]).filter(function(o, n, r) {
               return !!(null != i && i != o.tag || null != t && t != o.priority)
            }), gform.hooks[o][n] = r)
         }
      });
   </script>
   <title>{{$property_title}}</title>
   <meta name='robots' content='max-image-preview:large' />
   <style>
      img:is([sizes="auto" i], [sizes^="auto," i]) {
         contain-intrinsic-size: 3000px 1500px
      }
   </style>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="alternate" type="application/rss+xml" title="Property Info &raquo; Feed" href="https://propertyinfo.gr/feed/" />
   <link rel="alternate" type="application/rss+xml" title="Property Info &raquo; Comments Feed" href="https://propertyinfo.gr/comments/feed/" />
   <link rel="alternate" type="application/rss+xml" title="Property Info &raquo; Villa Kalipso Comments Feed" href="https://propertyinfo.gr/property/11754/feed/" />
   <script>
      window._wpemojiSettings = {
         "baseUrl": "https:\/\/s.w.org\/images\/core\/emoji\/15.1.0\/72x72\/",
         "ext": ".png",
         "svgUrl": "https:\/\/s.w.org\/images\/core\/emoji\/15.1.0\/svg\/",
         "svgExt": ".svg",
         "source": {
            "concatemoji": "https:\/\/propertyinfo.gr\/wp-includes\/js\/wp-emoji-release.min.js?ver=6.8.1"
         }
      };
      /*! This file is auto-generated */
      ! function(i, n) {
         var o, s, e;

         function c(e) {
            try {
               var t = {
                  supportTests: e,
                  timestamp: (new Date).valueOf()
               };
               sessionStorage.setItem(o, JSON.stringify(t))
            } catch (e) {}
         }

         function p(e, t, n) {
            e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(t, 0, 0);
            var t = new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data),
               r = (e.clearRect(0, 0, e.canvas.width, e.canvas.height), e.fillText(n, 0, 0), new Uint32Array(e.getImageData(0, 0, e.canvas.width, e.canvas.height).data));
            return t.every(function(e, t) {
               return e === r[t]
            })
         }

         function u(e, t, n) {
            switch (t) {
               case "flag":
                  return n(e, "\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f", "\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f") ? !1 : !n(e, "\ud83c\uddfa\ud83c\uddf3", "\ud83c\uddfa\u200b\ud83c\uddf3") && !n(e, "\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f", "\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f");
               case "emoji":
                  return !n(e, "\ud83d\udc26\u200d\ud83d\udd25", "\ud83d\udc26\u200b\ud83d\udd25")
            }
            return !1
         }

         function f(e, t, n) {
            var r = "undefined" != typeof WorkerGlobalScope && self instanceof WorkerGlobalScope ? new OffscreenCanvas(300, 150) : i.createElement("canvas"),
               a = r.getContext("2d", {
                  willReadFrequently: !0
               }),
               o = (a.textBaseline = "top", a.font = "600 32px Arial", {});
            return e.forEach(function(e) {
               o[e] = t(a, e, n)
            }), o
         }

         function t(e) {
            var t = i.createElement("script");
            t.src = e, t.defer = !0, i.head.appendChild(t)
         }
         "undefined" != typeof Promise && (o = "wpEmojiSettingsSupports", s = ["flag", "emoji"], n.supports = {
            everything: !0,
            everythingExceptFlag: !0
         }, e = new Promise(function(e) {
            i.addEventListener("DOMContentLoaded", e, {
               once: !0
            })
         }), new Promise(function(t) {
            var n = function() {
               try {
                  var e = JSON.parse(sessionStorage.getItem(o));
                  if ("object" == typeof e && "number" == typeof e.timestamp && (new Date).valueOf() < e.timestamp + 604800 && "object" == typeof e.supportTests) return e.supportTests
               } catch (e) {}
               return null
            }();
            if (!n) {
               if ("undefined" != typeof Worker && "undefined" != typeof OffscreenCanvas && "undefined" != typeof URL && URL.createObjectURL && "undefined" != typeof Blob) try {
                  var e = "postMessage(" + f.toString() + "(" + [JSON.stringify(s), u.toString(), p.toString()].join(",") + "));",
                     r = new Blob([e], {
                        type: "text/javascript"
                     }),
                     a = new Worker(URL.createObjectURL(r), {
                        name: "wpTestEmojiSupports"
                     });
                  return void(a.onmessage = function(e) {
                     c(n = e.data), a.terminate(), t(n)
                  })
               } catch (e) {}
               c(n = f(s, u, p))
            }
            t(n)
         }).then(function(e) {
            for (var t in e) n.supports[t] = e[t], n.supports.everything = n.supports.everything && n.supports[t], "flag" !== t && (n.supports.everythingExceptFlag = n.supports.everythingExceptFlag && n.supports[t]);
            n.supports.everythingExceptFlag = n.supports.everythingExceptFlag && !n.supports.flag, n.DOMReady = !1, n.readyCallback = function() {
               n.DOMReady = !0
            }
         }).then(function() {
            return e
         }).then(function() {
            var e;
            n.supports.everything || (n.readyCallback(), (e = n.source || {}).concatemoji ? t(e.concatemoji) : e.wpemoji && e.twemoji && (t(e.twemoji), t(e.wpemoji)))
         }))
      }((window, document), window._wpemojiSettings);
   </script>
   <script>
   function initMap() {
      console.log({{ floatval($latitude) }})
       var location = { lat: {{ floatval($latitude) }}, lng: {{ floatval($longitude) }} };
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
   <style id='wp-emoji-styles-inline-css'>
      img.wp-smiley,
      img.emoji {
         display: inline !important;
         border: none !important;
         box-shadow: none !important;
         height: 1em !important;
         width: 1em !important;
         margin: 0 0.07em !important;
         vertical-align: -0.1em !important;
         background: none !important;
         padding: 0 !important;
      }
   </style>
   <link rel='stylesheet' id='wp-block-library-css' href='https://propertyinfo.gr/wp-includes/css/dist/block-library/style.min.css?ver=6.8.1' media='all' />
   <style id='classic-theme-styles-inline-css'>
      /*! This file is auto-generated */
      .wp-block-button__link {
         color: #fff;
         background-color: #32373c;
         border-radius: 9999px;
         box-shadow: none;
         text-decoration: none;
         padding: calc(.667em + 2px) calc(1.333em + 2px);
         font-size: 1.125em
      }

      .wp-block-file__button {
         background: #32373c;
         color: #fff;
         text-decoration: none
      }
   </style>
   <style id='global-styles-inline-css'>
      :root {
         --wp--preset--aspect-ratio--square: 1;
         --wp--preset--aspect-ratio--4-3: 4/3;
         --wp--preset--aspect-ratio--3-4: 3/4;
         --wp--preset--aspect-ratio--3-2: 3/2;
         --wp--preset--aspect-ratio--2-3: 2/3;
         --wp--preset--aspect-ratio--16-9: 16/9;
         --wp--preset--aspect-ratio--9-16: 9/16;
         --wp--preset--color--black: #000000;
         --wp--preset--color--cyan-bluish-gray: #abb8c3;
         --wp--preset--color--white: #ffffff;
         --wp--preset--color--pale-pink: #f78da7;
         --wp--preset--color--vivid-red: #cf2e2e;
         --wp--preset--color--luminous-vivid-orange: #ff6900;
         --wp--preset--color--luminous-vivid-amber: #fcb900;
         --wp--preset--color--light-green-cyan: #7bdcb5;
         --wp--preset--color--vivid-green-cyan: #00d084;
         --wp--preset--color--pale-cyan-blue: #8ed1fc;
         --wp--preset--color--vivid-cyan-blue: #0693e3;
         --wp--preset--color--vivid-purple: #9b51e0;
         --wp--preset--color--contrast: var(--contrast);
         --wp--preset--color--contrast-2: var(--contrast-2);
         --wp--preset--color--contrast-3: var(--contrast-3);
         --wp--preset--color--base: var(--base);
         --wp--preset--color--base-2: var(--base-2);
         --wp--preset--color--base-3: var(--base-3);
         --wp--preset--color--accent: var(--accent);
         --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgba(6, 147, 227, 1) 0%, rgb(155, 81, 224) 100%);
         --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
         --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgba(252, 185, 0, 1) 0%, rgba(255, 105, 0, 1) 100%);
         --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgba(255, 105, 0, 1) 0%, rgb(207, 46, 46) 100%);
         --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
         --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
         --wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
         --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
         --wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
         --wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
         --wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
         --wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
         --wp--preset--font-size--small: 13px;
         --wp--preset--font-size--medium: 20px;
         --wp--preset--font-size--large: 36px;
         --wp--preset--font-size--x-large: 42px;
         --wp--preset--spacing--20: 0.44rem;
         --wp--preset--spacing--30: 0.67rem;
         --wp--preset--spacing--40: 1rem;
         --wp--preset--spacing--50: 1.5rem;
         --wp--preset--spacing--60: 2.25rem;
         --wp--preset--spacing--70: 3.38rem;
         --wp--preset--spacing--80: 5.06rem;
         --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
         --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
         --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
         --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);
         --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
      }

      :where(.is-layout-flex) {
         gap: 0.5em;
      }

      :where(.is-layout-grid) {
         gap: 0.5em;
      }

      body .is-layout-flex {
         display: flex;
      }

      .is-layout-flex {
         flex-wrap: wrap;
         align-items: center;
      }

      .is-layout-flex> :is(*, div) {
         margin: 0;
      }

      body .is-layout-grid {
         display: grid;
      }

      .is-layout-grid> :is(*, div) {
         margin: 0;
      }

      :where(.wp-block-columns.is-layout-flex) {
         gap: 2em;
      }

      :where(.wp-block-columns.is-layout-grid) {
         gap: 2em;
      }

      :where(.wp-block-post-template.is-layout-flex) {
         gap: 1.25em;
      }

      :where(.wp-block-post-template.is-layout-grid) {
         gap: 1.25em;
      }

      .has-black-color {
         color: var(--wp--preset--color--black) !important;
      }

      .has-cyan-bluish-gray-color {
         color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }

      .has-white-color {
         color: var(--wp--preset--color--white) !important;
      }

      .has-pale-pink-color {
         color: var(--wp--preset--color--pale-pink) !important;
      }

      .has-vivid-red-color {
         color: var(--wp--preset--color--vivid-red) !important;
      }

      .has-luminous-vivid-orange-color {
         color: var(--wp--preset--color--luminous-vivid-orange) !important;
      }

      .has-luminous-vivid-amber-color {
         color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }

      .has-light-green-cyan-color {
         color: var(--wp--preset--color--light-green-cyan) !important;
      }

      .has-vivid-green-cyan-color {
         color: var(--wp--preset--color--vivid-green-cyan) !important;
      }

      .has-pale-cyan-blue-color {
         color: var(--wp--preset--color--pale-cyan-blue) !important;
      }

      .has-vivid-cyan-blue-color {
         color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }

      .has-vivid-purple-color {
         color: var(--wp--preset--color--vivid-purple) !important;
      }

      .has-black-background-color {
         background-color: var(--wp--preset--color--black) !important;
      }

      .has-cyan-bluish-gray-background-color {
         background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }

      .has-white-background-color {
         background-color: var(--wp--preset--color--white) !important;
      }

      .has-pale-pink-background-color {
         background-color: var(--wp--preset--color--pale-pink) !important;
      }

      .has-vivid-red-background-color {
         background-color: var(--wp--preset--color--vivid-red) !important;
      }

      .has-luminous-vivid-orange-background-color {
         background-color: var(--wp--preset--color--luminous-vivid-orange) !important;
      }

      .has-luminous-vivid-amber-background-color {
         background-color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }

      .has-light-green-cyan-background-color {
         background-color: var(--wp--preset--color--light-green-cyan) !important;
      }

      .has-vivid-green-cyan-background-color {
         background-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }

      .has-pale-cyan-blue-background-color {
         background-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }

      .has-vivid-cyan-blue-background-color {
         background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }

      .has-vivid-purple-background-color {
         background-color: var(--wp--preset--color--vivid-purple) !important;
      }

      .has-black-border-color {
         border-color: var(--wp--preset--color--black) !important;
      }

      .has-cyan-bluish-gray-border-color {
         border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }

      .has-white-border-color {
         border-color: var(--wp--preset--color--white) !important;
      }

      .has-pale-pink-border-color {
         border-color: var(--wp--preset--color--pale-pink) !important;
      }

      .has-vivid-red-border-color {
         border-color: var(--wp--preset--color--vivid-red) !important;
      }

      .has-luminous-vivid-orange-border-color {
         border-color: var(--wp--preset--color--luminous-vivid-orange) !important;
      }

      .has-luminous-vivid-amber-border-color {
         border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }

      .has-light-green-cyan-border-color {
         border-color: var(--wp--preset--color--light-green-cyan) !important;
      }

      .has-vivid-green-cyan-border-color {
         border-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }

      .has-pale-cyan-blue-border-color {
         border-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }

      .has-vivid-cyan-blue-border-color {
         border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }

      .has-vivid-purple-border-color {
         border-color: var(--wp--preset--color--vivid-purple) !important;
      }

      .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
         background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;
      }

      .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
         background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;
      }

      .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
         background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;
      }

      .has-luminous-vivid-orange-to-vivid-red-gradient-background {
         background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;
      }

      .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
         background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;
      }

      .has-cool-to-warm-spectrum-gradient-background {
         background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;
      }

      .has-blush-light-purple-gradient-background {
         background: var(--wp--preset--gradient--blush-light-purple) !important;
      }

      .has-blush-bordeaux-gradient-background {
         background: var(--wp--preset--gradient--blush-bordeaux) !important;
      }

      .has-luminous-dusk-gradient-background {
         background: var(--wp--preset--gradient--luminous-dusk) !important;
      }

      .has-pale-ocean-gradient-background {
         background: var(--wp--preset--gradient--pale-ocean) !important;
      }

      .has-electric-grass-gradient-background {
         background: var(--wp--preset--gradient--electric-grass) !important;
      }

      .has-midnight-gradient-background {
         background: var(--wp--preset--gradient--midnight) !important;
      }

      .has-small-font-size {
         font-size: var(--wp--preset--font-size--small) !important;
      }

      .has-medium-font-size {
         font-size: var(--wp--preset--font-size--medium) !important;
      }

      .has-large-font-size {
         font-size: var(--wp--preset--font-size--large) !important;
      }

      .has-x-large-font-size {
         font-size: var(--wp--preset--font-size--x-large) !important;
      }

      :where(.wp-block-post-template.is-layout-flex) {
         gap: 1.25em;
      }

      :where(.wp-block-post-template.is-layout-grid) {
         gap: 1.25em;
      }

      :where(.wp-block-columns.is-layout-flex) {
         gap: 2em;
      }

      :where(.wp-block-columns.is-layout-grid) {
         gap: 2em;
      }

      :root :where(.wp-block-pullquote) {
         font-size: 1.5em;
         line-height: 1.6;
      }
   </style>
   <link rel='stylesheet' id='icomoon-style-css' href='https://propertyinfo.gr/wp-content/themes/generatepress_child/assets/fonts/icomoon/style.css?ver=6.8.1' media='all' />
   <link rel='stylesheet' id='tiny-slider-css' href='https://propertyinfo.gr/wp-content/themes/generatepress_child/assets/css/tiny-slider.css?ver=6.8.1' media='all' />
   <link rel='stylesheet' id='fonts-style-css' href='https://propertyinfo.gr/wp-content/themes/generatepress_child/assets/css/fonts.css?ver=6.8.1' media='all' />
   <link rel='stylesheet' id='stlys-css' href='https://propertyinfo.gr/wp-content/themes/generatepress_child/assets/css/style.css?ver=6.8.1' media='all' />
   <link rel='stylesheet' id='generate-comments-css' href='https://propertyinfo.gr/wp-content/themes/generatepress/assets/css/components/comments.min.css?ver=3.3.0' media='all' />
   <link rel='stylesheet' id='generate-widget-areas-css' href='https://propertyinfo.gr/wp-content/themes/generatepress/assets/css/components/widget-areas.min.css?ver=3.3.0' media='all' />
   <link rel='stylesheet' id='generate-style-css' href='https://propertyinfo.gr/wp-content/themes/generatepress/assets/css/main.min.css?ver=3.3.0' media='all' />
   <style id='generate-style-inline-css'>
      body {
         background-color: var(--base-2);
         color: var(--contrast);
      }

      a {
         color: var(--accent);
      }

      a {
         text-decoration: underline;
      }

      .entry-title a,
      .site-branding a,
      a.button,
      .wp-block-button__link,
      .main-navigation a {
         text-decoration: none;
      }

      a:hover,
      a:focus,
      a:active {
         color: var(--contrast);
      }

      .wp-block-group__inner-container {
         max-width: 1200px;
         margin-left: auto;
         margin-right: auto;
      }

      :root {
         --contrast: #222222;
         --contrast-2: #575760;
         --contrast-3: #b2b2be;
         --base: #f0f0f0;
         --base-2: #f7f8f9;
         --base-3: #ffffff;
         --accent: #1e73be;
      }

      :root .has-contrast-color {
         color: var(--contrast);
      }

      :root .has-contrast-background-color {
         background-color: var(--contrast);
      }

      :root .has-contrast-2-color {
         color: var(--contrast-2);
      }

      :root .has-contrast-2-background-color {
         background-color: var(--contrast-2);
      }

      :root .has-contrast-3-color {
         color: var(--contrast-3);
      }

      :root .has-contrast-3-background-color {
         background-color: var(--contrast-3);
      }

      :root .has-base-color {
         color: var(--base);
      }

      :root .has-base-background-color {
         background-color: var(--base);
      }

      :root .has-base-2-color {
         color: var(--base-2);
      }

      :root .has-base-2-background-color {
         background-color: var(--base-2);
      }

      :root .has-base-3-color {
         color: var(--base-3);
      }

      :root .has-base-3-background-color {
         background-color: var(--base-3);
      }

      :root .has-accent-color {
         color: var(--accent);
      }

      :root .has-accent-background-color {
         background-color: var(--accent);
      }

      .top-bar {
         background-color: #636363;
         color: #ffffff;
      }

      .top-bar a {
         color: #ffffff;
      }

      .top-bar a:hover {
         color: #303030;
      }

      .site-header {
         background-color: var(--base-3);
      }

      .main-title a,
      .main-title a:hover {
         color: var(--contrast);
      }

      .site-description {
         color: var(--contrast-2);
      }

      .mobile-menu-control-wrapper .menu-toggle,
      .mobile-menu-control-wrapper .menu-toggle:hover,
      .mobile-menu-control-wrapper .menu-toggle:focus,
      .has-inline-mobile-toggle #site-navigation.toggled {
         background-color: rgba(0, 0, 0, 0.02);
      }

      .main-navigation,
      .main-navigation ul ul {
         background-color: var(--base-3);
      }

      .main-navigation .main-nav ul li a,
      .main-navigation .menu-toggle,
      .main-navigation .menu-bar-items {
         color: var(--contrast);
      }

      .main-navigation .main-nav ul li:not([class*="current-menu-"]):hover>a,
      .main-navigation .main-nav ul li:not([class*="current-menu-"]):focus>a,
      .main-navigation .main-nav ul li.sfHover:not([class*="current-menu-"])>a,
      .main-navigation .menu-bar-item:hover>a,
      .main-navigation .menu-bar-item.sfHover>a {
         color: var(--accent);
      }

      button.menu-toggle:hover,
      button.menu-toggle:focus {
         color: var(--contrast);
      }

      .main-navigation .main-nav ul li[class*="current-menu-"]>a {
         color: var(--accent);
      }

      .navigation-search input[type="search"],
      .navigation-search input[type="search"]:active,
      .navigation-search input[type="search"]:focus,
      .main-navigation .main-nav ul li.search-item.active>a,
      .main-navigation .menu-bar-items .search-item.active>a {
         color: var(--accent);
      }

      .main-navigation ul ul {
         background-color: var(--base);
      }

      .separate-containers .inside-article,
      .separate-containers .comments-area,
      .separate-containers .page-header,
      .one-container .container,
      .separate-containers .paging-navigation,
      .inside-page-header {
         background-color: var(--base-3);
      }

      .entry-title a {
         color: var(--contrast);
      }

      .entry-title a:hover {
         color: var(--contrast-2);
      }

      .entry-meta {
         color: var(--contrast-2);
      }

      .sidebar .widget {
         background-color: var(--base-3);
      }

      .footer-widgets {
         background-color: var(--base-3);
      }

      .site-info {
         background-color: var(--base-3);
      }

      input[type="text"],
      input[type="email"],
      input[type="url"],
      input[type="password"],
      input[type="search"],
      input[type="tel"],
      input[type="number"],
      textarea,
      select {
         color: var(--contrast);
         background-color: var(--base-2);
         border-color: var(--base);
      }

      input[type="text"]:focus,
      input[type="email"]:focus,
      input[type="url"]:focus,
      input[type="password"]:focus,
      input[type="search"]:focus,
      input[type="tel"]:focus,
      input[type="number"]:focus,
      textarea:focus,
      select:focus {
         color: var(--contrast);
         background-color: var(--base-2);
         border-color: var(--contrast-3);
      }

      button,
      html input[type="button"],
      input[type="reset"],
      input[type="submit"],
      a.button,
      a.wp-block-button__link:not(.has-background) {
         color: #ffffff;
         background-color: #55555e;
      }

      button:hover,
      html input[type="button"]:hover,
      input[type="reset"]:hover,
      input[type="submit"]:hover,
      a.button:hover,
      button:focus,
      html input[type="button"]:focus,
      input[type="reset"]:focus,
      input[type="submit"]:focus,
      a.button:focus,
      a.wp-block-button__link:not(.has-background):active,
      a.wp-block-button__link:not(.has-background):focus,
      a.wp-block-button__link:not(.has-background):hover {
         color: #ffffff;
         background-color: #3f4047;
      }

      a.generate-back-to-top {
         background-color: rgba(0, 0, 0, 0.4);
         color: #ffffff;
      }

      a.generate-back-to-top:hover,
      a.generate-back-to-top:focus {
         background-color: rgba(0, 0, 0, 0.6);
         color: #ffffff;
      }

      :root {
         --gp-search-modal-bg-color: var(--base-3);
         --gp-search-modal-text-color: var(--contrast);
         --gp-search-modal-overlay-bg-color: rgba(0, 0, 0, 0.2);
      }

      @media (max-width:768px) {

         .main-navigation .menu-bar-item:hover>a,
         .main-navigation .menu-bar-item.sfHover>a {
            background: none;
            color: var(--contrast);
         }
      }

      .nav-below-header .main-navigation .inside-navigation.grid-container,
      .nav-above-header .main-navigation .inside-navigation.grid-container {
         padding: 0px 20px 0px 20px;
      }

      .site-main .wp-block-group__inner-container {
         padding: 40px;
      }

      .separate-containers .paging-navigation {
         padding-top: 20px;
         padding-bottom: 20px;
      }

      .entry-content .alignwide,
      body:not(.no-sidebar) .entry-content .alignfull {
         margin-left: -40px;
         width: calc(100% + 80px);
         max-width: calc(100% + 80px);
      }

      .rtl .menu-item-has-children .dropdown-menu-toggle {
         padding-left: 20px;
      }

      .rtl .main-navigation .main-nav ul li.menu-item-has-children>a {
         padding-right: 20px;
      }

      @media (max-width:768px) {

         .separate-containers .inside-article,
         .separate-containers .comments-area,
         .separate-containers .page-header,
         .separate-containers .paging-navigation,
         .one-container .site-content,
         .inside-page-header {
            padding: 30px;
         }

         .site-main .wp-block-group__inner-container {
            padding: 30px;
         }

         .inside-top-bar {
            padding-right: 30px;
            padding-left: 30px;
         }

         .inside-header {
            padding-right: 30px;
            padding-left: 30px;
         }

         .widget-area .widget {
            padding-top: 30px;
            padding-right: 30px;
            padding-bottom: 30px;
            padding-left: 30px;
         }

         .footer-widgets-container {
            padding-top: 30px;
            padding-right: 30px;
            padding-bottom: 30px;
            padding-left: 30px;
         }

         .inside-site-info {
            padding-right: 30px;
            padding-left: 30px;
         }

         .entry-content .alignwide,
         body:not(.no-sidebar) .entry-content .alignfull {
            margin-left: -30px;
            width: calc(100% + 60px);
            max-width: calc(100% + 60px);
         }

         .one-container .site-main .paging-navigation {
            margin-bottom: 20px;
         }
      }

      /* End cached CSS */
      .is-right-sidebar {
         width: 30%;
      }

      .is-left-sidebar {
         width: 30%;
      }

      .site-content .content-area {
         width: 70%;
      }

      @media (max-width:768px) {

         .main-navigation .menu-toggle,
         .sidebar-nav-mobile:not(#sticky-placeholder) {
            display: block;
         }

         .main-navigation ul,
         .gen-sidebar-nav,
         .main-navigation:not(.slideout-navigation):not(.toggled) .main-nav>ul,
         .has-inline-mobile-toggle #site-navigation .inside-navigation>*:not(.navigation-search):not(.main-nav) {
            display: none;
         }

         .nav-align-right .inside-navigation,
         .nav-align-center .inside-navigation {
            justify-content: space-between;
         }
      }

      .elementor-template-full-width .site-content {
         display: block;
      }
   </style>
   <link rel='stylesheet' id='generate-child-css' href='https://propertyinfo.gr/wp-content/themes/generatepress_child/style.css?ver=1683106042' media='all' />
   <link rel='stylesheet' id='elementor-lazyload-css' href='https://propertyinfo.gr/wp-content/plugins/elementor/assets/css/modules/lazyload/frontend.min.css?ver=3.13.0' media='all' />
   <link rel='stylesheet' id='elementor-frontend-css' href='https://propertyinfo.gr/wp-content/plugins/elementor/assets/css/frontend-lite.min.css?ver=3.13.0' media='all' />
   <link rel='stylesheet' id='swiper-css' href='https://propertyinfo.gr/wp-content/plugins/elementor/assets/lib/swiper/css/swiper.min.css?ver=5.3.6' media='all' />
   <link rel='stylesheet' id='elementor-post-5-css' href='https://propertyinfo.gr/wp-content/uploads/elementor/css/post-5.css?ver=1683612084' media='all' />
   <link rel='stylesheet' id='powerpack-frontend-css' href='https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/css/min/frontend.min.css?ver=2.9.16' media='all' />
   <link rel='stylesheet' id='elementor-pro-css' href='https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/css/frontend-lite.min.css?ver=3.13.0' media='all' />
   <link rel='stylesheet' id='elementor-post-12556-css' href='https://propertyinfo.gr/wp-content/uploads/elementor/css/post-12556.css?ver=1706365951' media='all' />
   <link rel='stylesheet' id='google-fonts-1-css' href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CHeebo%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=auto&#038;ver=6.8.1' media='all' />
   <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
   <script id="gk-gravityview-blocks-js-extra">
      var gkGravityViewBlocks = {
         "home_page": "https:\/\/propertyinfo.gr",
         "ajax_url": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php",
         "create_new_view_url": "https:\/\/propertyinfo.gr\/wp-admin\/post-new.php?post_type=gravityview",
         "edit_view_url": "https:\/\/propertyinfo.gr\/wp-admin\/post.php?action=edit&post=%s",
         "views": [],
         "gk-gravityview-blocks\/entry": {
            "previewImage": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityview\/future\/includes\/gutenberg\/blocks\/entry\/preview.svg"
         },
         "gk-gravityview-blocks\/entry-field": {
            "previewImage": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityview\/future\/includes\/gutenberg\/blocks\/entry-field\/preview.svg"
         },
         "gk-gravityview-blocks\/entry-link": {
            "previewImage": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityview\/future\/includes\/gutenberg\/blocks\/entry-link\/preview.svg"
         },
         "gk-gravityview-blocks\/view": {
            "previewImage": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityview\/future\/includes\/gutenberg\/blocks\/view\/preview.svg"
         },
         "gk-gravityview-blocks\/view-details": {
            "previewImage": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityview\/future\/includes\/gutenberg\/blocks\/view-details\/preview.svg"
         }
      };
   </script>
   <link rel="https://api.w.org/" href="https://propertyinfo.gr/wp-json/" />
   <link rel="alternate" title="JSON" type="application/json" href="https://propertyinfo.gr/wp-json/wp/v2/property/11921" />
   <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://propertyinfo.gr/xmlrpc.php?rsd" />
   <meta name="generator" content="WordPress 6.8.1" />
   <link rel="canonical" href="https://propertyinfo.gr/property/11754/" />
   <link rel='shortlink' href='https://propertyinfo.gr/?p=11921' />
   <link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed" href="https://propertyinfo.gr/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fpropertyinfo.gr%2Fproperty%2F11754%2F" />
   <link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed" href="https://propertyinfo.gr/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fpropertyinfo.gr%2Fproperty%2F11754%2F&#038;format=xml" />
   <meta name="generator" content="Elementor 3.13.0; features: e_dom_optimization, e_optimized_assets_loading, e_optimized_css_loading, e_font_icon_svg, a11y_improvements, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-auto">
   <style>
      .recentcomments a {
         display: inline !important;
         padding: 0 !important;
         margin: 0 !important;
      }
   </style>
   <link rel="icon" href="https://propertyinfo.gr/wp-content/uploads/2021/10/favicon.png" sizes="32x32" />
   <link rel="icon" href="https://propertyinfo.gr/wp-content/uploads/2021/10/favicon.png" sizes="192x192" />
   <link rel="apple-touch-icon" href="https://propertyinfo.gr/wp-content/uploads/2021/10/favicon.png" />
   <meta name="msapplication-TileImage" content="https://propertyinfo.gr/wp-content/uploads/2021/10/favicon.png" />
   <style id="wp-custom-css">
      /* amenities icon color */
      #cspml_listings_filter_form .irs--round .irs-handle {
         cursor: grab;
         border: 2px solid #a06957 !important;
      }

      #cspml_listings_filter_form .irs-from,
      #cspml_listings_filter_form .irs-to,
      #cspml_listings_filter_form .irs-single {
         background: #a06957 !important;
      }

      #cspml_listings_filter_form .irs-from:after,
      #cspml_listings_filter_form .irs-to:after,
      #cspml_listings_filter_form .irs-single:after,
      #cspml_listings_filter_form .irs--round .irs-from:before,
      #cspml_listings_filter_form .irs--round .irs-to:before,
      #cspml_listings_filter_form .irs--round .irs-single:before {
         border-top-color: #a06957 !important;
      }

      #cspml_listings_filter_form .irs--round .irs-bar {
         background-color: #a06957 !important;
      }

      .codespacing_progress_map_area .cspm_bg_hex,
      .codespacing_progress_map_area .cspm_bg_hex_hover,
      .codespacing_progress_map_area .cspm_bg_before_hex:before,
      .codespacing_progress_map_area .cspm_bg_after_hex:after {
         background-color: #a06957 !important;
      }

      div.codespacing_progress_map_area svg.cspm_svg_colored * {
         fill: #a06957 !important;
      }

      .cspm-row div[class^=cspml_pagination_] ul li span.current {
         background-color: #a06957 !important;
      }

      .cspm-row .cspm_txt_hex,
      .cspm-row .cspm_link_hex a,
      .cspm-row .cspm_txt_hex_hover,
      .cspm-row .cspm_txt_hex_hover a,
      .cspm-row .cspm_marker_overlay button.si-close-button {
         color: #008fed !important;
      }

      .gf_login_links a:first-child,
      .gf_login_links br {
         display: none;
      }

      .irs--round .irs-handle {
         cursor: grab;
         border: 2px solid #a06957 !important;
      }

      #cspml_listings_filter_form .irs--round .irs-handle {
         cursor: grab;
         border: 2px solid #a06957 !important;
      }

      .irs--round .irs-handle {
         cursor: grab;
         border: 2px solid #a06957 !important;
      }

      .loginbtn {
         margin-top: 20px;
      }

      .loginbtn a {
         color: white;
         background-color: #a06957;
         padding: 4px 15px;
         border-radius: 7px;
         line-height: 0px !important;
      }

      .site-footer {
         padding: 0px !important;
         display: none;
      }

      div.cspml_item img.thumb {
         max-height: 170px !important;
         object-fit: cover !important;
      }

      .page-id-14734 .gform_wrapper.gravity-theme .gfield input,
      .gform_wrapper.gravity-theme .gfield select {
         max-width: 100%;
         width: 100%;
      }

      input#choice_3 {
         width: auto;
      }

      nav.gf_login_links a {
         background: #a06957;
         color: white;
         padding: 10px 20px;
         border-radius: 9px;
      }

      div.cspml_details_container div.cspml_details_content {
         height: 200px !important;
      }

      .irs--round .irs-bar {
         background-color: #a06957 !important;
      }

      .irs-from,
      .irs-to,
      .irs-single {
         background: #a06957 !important;
      }

      .irs-from:after,
      .irs-to:after,
      .irs-single:after,
      .irs--round .irs-from:before,
      .irs--round .irs-to:before,
      .irs--round .irs-single:before {
         border-top-color: #a06957 !important;
      }

      .irs--round .irs-handle {
         box-shadow: 0 1px 3px #a06957;
      }

      .ae-icon-list-icon {
         color: blue !important;
      }

      .inside-header {
         display: none;
      }

      .inside-navigation.grid-container {
         display: none;
      }

      li#menu-item-14797 a {
         background-color: #a06957 !important;
         color: white !important;
         /* padding: 0px; */
         margin-bottom: 10px;
         border-radius: 11px;
         padding: 8px 12px !important;
         line-height: 27px;
      }

      /* .inside-header.grid-container {
         display: none;
         } */
      .single-property .gform_wrapper.gravity-theme .gfield textarea.medium {
         height: 105px !important;
      }

      button,
      input[type=button],
      input[type=reset],
      input[type=submit] {
         background: #a06957;
      }

      .gform_wrapper.gravity-theme .gform_drop_area {
         padding: 9px !important;
      }

      .button,
      .wp-block-button .wp-block-button__link {
         padding: 6px 20px;
         border-radius: 8px;
         background-color: #a06957;
      }

      .upersocial {
         padding-left: 36px;
      }

      .dwnsocial {
         margin-top: -54px;
      }

      .albumback .elementor-button.elementor-size-xl {
         font-size: 44px;
         padding: 12px 17px;
         border-radius: 6px;
      }

      .customfeature .elementor-motion-effects-layer {
         height: 110% !important;
      }

      .videohe .elementor-widget-video .elementor-wrapper iframe,
      .elementor-widget-video .elementor-wrapper video {
         height: 366px;
      }

      .content-text p.elementor-icon-box-description {
         font-size: 15px !important;
      }

      .content-text .elementor-icon-box-title {
         font-size: 15px !important;
         line-height: 20px !important;
      }

      .rowshadwo {
         box-shadow: 0 10px 31px 0 rgba(7, 152, 255, .09);
      }

      h5.catetype {
         color: white;
         font-weight: 400;
      }

      .owl-carousel .active:after {
         display: none;
      }

      /* gf hide request no budget */
      .gchoice_3_22_2 {
         visibility: hidden;
      }

      /* Single Property CSS */
      .hero.page-inner.custmhero {
         height: 370px;
         min-height: 400px;
         background-size: cover;
         background-position: center;
      }

      .owl-theme .owl-nav [class*=owl-] {
         color: #a06957 !important;
         font-weight: 900 !important;
      }

      .property-item.mb-30 {
         margin-bottom: 30px;
         box-shadow: 1px 3px 10px #ccc;
         border-radius: 81px;
         /* border-radius: 81px !important; */
      }

      span.d-block.d-flex.align-items-center.me-333 {
         /* padding-left: 11px; */
         margin-right: 2.5%;
      }

      h6.text-primary.mb-4 a {
         color: #a06957;
         font-size: 17px;
         font-weight: 800;
      }

      .custheading {
         color: #a06957;
         font-weight: 700;
         padding-bottom: 13px;
         font-size: 20px;
         padding-top: 15px;
      }

      .bg-box p {
         margin-bottom: 1.1em;
         font-family: 'Roboto';
         font-size: 14px;
         line-height: 22px;
      }

      #citydiv {
         display: none !important;
      }

      .single_property_labels a {
         color: white;
      }

      a.acc_google_maps {
         color: white !important;
      }

      .hero .light-heading {
         font-weight: 600 !important;
         line-height: 1;
         font-size: 52px !important;
      }

      .property-item .property-content {
         background: #fff;
         padding: 19px !important;
         border-radius: 20px;
      }

      span.d-block.mb-2.text-black-50 {
         color: rgb(0 0 0 / 74%) !important;
      }

      .property-item .property-content .price span:after {
         height: 1px !important;
      }

      .cstmic {
         font-weight: 700;
         font-size: 16px !important;
         color: #a06957 !important;
         padding-right: 5px;
      }

      .me-32 {
         margin-right: 8rem !important;
      }

      .location-id span.city.price.d-block.mb-3 {
         font-size: 16px;
         font-weight: 500;
         color: #000000a3;
      }

      .location-id span.city.price.d-block.mb-3 {
         padding-left: 1px;
         padding-right: 11px;
      }

      .location-id {
         display: flex;
      }

      span.property-id {
         padding-left: 10px;
      }

      .property-categories__description {
         font-size: 26px;
         font-weight: 600;
         margin-bottom: 15px;
      }

      .custmhero .row {
         height: 100px !important;
         min-height: 92px !important;
         padding-top: 8%;
      }

      .single-property .grid-container {
         max-width: 100%;
         padding-left: 0;
         padding-right: 0;
      }

      .grid-container {
         padding-left: 0px !important;
         padding-right: 0px !important;
      }

      h3.mb-0 {
         color: #a06957;
         font-weight: 700;
      }

      .hero .sub-heading {
         color: #fff;
         font-weight: 800;
         font-size: 17px;
      }

      .hero .heading {
         font-weight: 600 !important;
      }

      .section .heading {
         font-weight: 700;
         font-size: 29px !important;
         color: #a06957 !important;
      }

      h4.heading-title {
         color: var(--e-global-color-a8c7729);
         font-family: "Roboto", Sans-serif;
         font-size: 35px;
         font-weight: 800;
         line-height: 44px;
      }

      .site-footer {
         padding: 0px !important;
      }

      .page-template-properties .section {
         padding-top: 2.4rem !important;
         padding-bottom: 1rem !important;
      }

      /* GF gsection */
      .site-content {
         display: block !important;
         background-color: #fff;
      }

      .gsection_title {
         font-size: 16px;
         !important;
         margin-bottom: 5px !important;
      }

      .gsection_description {
         padding-top: 0px !important;
      }

      .shadow {
         box-shadow: 0px 4px 4px rgb(0 0 0 / 25%) !important;
      }

      span.newcol {
         font-size: 70px;
         line-height: 46px;
         color: white;
      }

      .reqfrm div#gform_wrapper_12 {
         background-color: #fff !important;
      }

      .reqfrm h2.gform_title {
         color: var(--e-global-color-a8c7729);
         font-family: "Roboto", Sans-serif;
         font-size: 45px;
         font-weight: 800;
         line-height: 34px;
      }

      .page-id-14101 .margin-bottom-15 {
         margin-bottom: 0px !important;
      }

      .homefrm h2.gform_submission_error {
         color: white !important;
      }

      .leftext {
         float: left;
      }

      .cspml_details_title.cspm_txt_hex_hover.cspm-col-lg-12.cspm-col-xs-12.cspm-col-sm-12.cspm-col-md-12 a {
         font-size: 20px;
         font-weight: 700;
         color: #a06957;
      }

      span.textcl {
         color: #a06957;
         font-weight: 500;
      }

      span.sptext .me-2 {
         color: #a06957;
      }

      span.sptext {
         margin-right: 10px;
      }

      .homefrm .gform_wrapper.gravity-theme .gform_validation_errors>ol a {
         color: #fff !important;
         font-size: 13.2px;
      }

      .homefrm .gform_wrapper.gravity-theme .gform_validation_errors>ol li {
         color: #fff;
      }

      .page-template-properties .grid-container {
         max-width: 100% !important;
      }

      .featured-image.page-header-image.grid-container.grid-parent {
         display: none;
      }

      /* New Style */
      div.cspml_item img.thumb {
         max-height: 196px;
         width: 100%;
         object-fit: cover;
      }

      div.cspm_bg_hex,
      span.cspm_bg_hex_hover,
      div.cspm_bg_before_hex:before,
      div.cspm_bg_after_hex:after {
         background-color: #a06957 !important;
      }

      div.cspm_txt_hex,
      .cspm_link_hex a,
      .cspm_txt_hex_hover,
      div.cspm_txt_hex_hover a,
      div.cspm_marker_overlay button.si-close-button {
         color: #a06957 !important;
      }

      div.cspml_details_container div.cspml_details_title a {
         display: inline-block;
         position: relative;
      }

      div.cspml_details_container div.cspml_details_title a:after {
         position: absolute;
         content: "";
         width: 100%;
         height: 1px;
         left: 0;
         bottom: 0;
         background-color: #a06957;
      }

      div.cspm_bg_hex,
      a.cspm_bg_hex_hover,
      div.cspm_bg_before_hex:before,
      div.cspm_bg_after_hex:after {
         background-color: #a06957 !important;
      }

      div.cspm_bg_rgb,
      div.cspm_bg_rgb_hover,
      div.cspm_bg_before_rgb:before,
      .cspm_bg_after_rgb:after {
         background-color: #a06957 !important;
      }

      div.cspml_details_container div {
         margin-bottom: 5px;
      }

      div.cspml_details_container div.cspml_details_title {
         padding: 0 0 10px;
      }

      div.cspml_details_container div {
         color: #000;
         font-size: 13px;
         font-weight: normal;
      }

      .leftext {
         display: inline-flex;
         justify-content: flex-start;
         width: 50%;
      }

      .righttext {
         display: inline-flex;
         justify-content: flex-end;
         width: 50%;
         font-size: 16px !important;
         font-weight: 500 !important;
         color: #000000a3 !important;
      }

      div.cspml_fs_label span.cspml_toggle_btn {
         padding: 5px 0 !important;
      }

      .selectize-control.single .selectize-input,
      .selectize-control.single .selectize-input input {
         cursor: pointer;
         text-transform: capitalize !important;
      }

      .selectize-dropdown-content {
         padding: 5px 0;
         text-transform: capitalize !important;
      }

      form.cspml_filter_form div.cspml_fs_options_list {
         padding: 8px 15px !important;
      }

      .leftext {
         display: inline-flex;
         justify-content: flex-start;
         width: 50%;
         font-size: 16px !important;
         font-weight: 500 !important;
         color: #000000a3 !important;
      }

      div.cspml_fs_label {
         padding: 5px 10px !important;
      }

      .leftext .textcl,
      .righttext .textcl {
         padding-right: 3px;
         font-size: 14px !important;
         font-weight: 500 !important;
         color: #000000a3 !important;
      }

      .cspm_border_shadow {
         box-shadow: 1px 3px 10px #ccc;
      }

      #cspml_listings_container svg.cspm_svg_colored * {
         fill: #008fed !important;
      }

      .fancybox-navigation .fancybox-button--arrow_right {
         background-color: #a06957 !important;
         color: #fff;
      }

      .fancybox-navigation .fancybox-button--arrow_left {
         background-color: #a06957 !important;
         color: #fff;
      }

      .pp-video-container {
         background-color: #a06957;
      }

      img.pp-video-thumb.entered.lazyloaded {
         display: none;
      }

      .pp-video-container {
         position: relative;
      }

      .rmvid.elementor-aspect-ratio-169 .elementor-fit-aspect-ratio {
         padding-bottom: 100%;
      }

      .rmvid .elementor-widget-container {
         max-width: 90px;
         margin: auto;
      }

      @media (min-width: 320px) and (max-width: 767px) {
         .albumback {
            display: flex !important;
            justify-content: center !important;
         }

         .rmvid.elementor-aspect-ratio-169 .elementor-fit-aspect-ratio {
            padding-bottom: 100%;
         }

         .rmvid .elementor-widget-container {
            max-width: 90px;
            margin: auto;
         }

         .elementor-14281 .elementor-element.elementor-element-f9dd95e {
            padding: 0em 0em 0em 0em !important;
         }

         .elementor-14179 .elementor-element.elementor-element-70eec33 .pp-image-gallery {
            margin-left: 0px !important;
         }

         .d-flex.align-items-center {
            padding: 6px 0px;
         }

         .cstmleftcl .cstmcl {
            text-align: left !important;
            padding-top: 10px;
         }

         .cstmleftcl ul {
            margin-left: 0;
         }

         span.newcol {
            font-size: 37px !important;
         }

         a.pp-button.elementor-button.elementor-size-sm.elementor-repeater-item-e7a525d {
            margin-top: 5%;
         }

         .reqfrm h2.gform_title {
            font-size: 35px;
            line-height: 37px;
         }

         .hero .light-heading {
            font-weight: 600 !important;
            line-height: 1.2 !important;
            font-size: 38px !important;
            letter-spacing: 0px !important;
         }

         .page-template-properties .box-content {
            margin-bottom: 12px;
         }

         .page-template-properties .section {
            padding-top: 2.4rem !important;
            padding-bottom: 1rem !important;
         }

         .upersocial {
            padding-left: 0px;
         }
      }
   </style>
   <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
</head>

<body class="wp-singular property-template-default single single-property postid-11921 single-format-standard wp-custom-logo wp-embed-responsive wp-theme-generatepress wp-child-theme-generatepress_child right-sidebar nav-above-header separate-containers nav-aligned-center header-aligned-center dropdown-hover featured-image-active e-lazyload elementor-default elementor-template-canvas elementor-kit-5 elementor-page-12556">
   <div data-elementor-type="single-post" data-elementor-id="12556" class="elementor elementor-12556 elementor-location-single post-11921 property type-property status-publish format-standard has-post-thumbnail hentry category-real-estate city-athens city-kavala city-patras">
      <section class="elementor-section elementor-top-section elementor-element elementor-element-4b1f49ea customfeature elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="4b1f49ea" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;background_motion_fx_motion_fx_scrolling&quot;:&quot;yes&quot;,&quot;background_motion_fx_translateY_effect&quot;:&quot;yes&quot;,&quot;background_motion_fx_translateY_speed&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:7,&quot;sizes&quot;:[]},&quot;background_motion_fx_devices&quot;:[&quot;desktop&quot;,&quot;tablet&quot;],&quot;background_motion_fx_translateY_affectedRange&quot;:{&quot;unit&quot;:&quot;%&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:{&quot;start&quot;:0,&quot;end&quot;:100}}}" style="background-image: url({{$property_external_primary_image}});">
         <div class="elementor-background-overlay"></div>
         <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-2ab9dec3" data-id="2ab9dec3" data-element_type="column">
               <div class="elementor-widget-wrap elementor-element-populated">
                  <div class="elementor-element elementor-element-1f03a3fe elementor-widget elementor-widget-heading" data-id="1f03a3fe" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <style>
                           /*! elementor - v3.13.0 - 08-05-2023 */
                           .elementor-heading-title {
                              padding: 0;
                              margin: 0;
                              line-height: 1
                           }

                           .elementor-widget-heading .elementor-heading-title[class*=elementor-size-]>a {
                              color: inherit;
                              font-size: inherit;
                              line-height: inherit
                           }

                           .elementor-widget-heading .elementor-heading-title.elementor-size-small {
                              font-size: 15px
                           }

                           .elementor-widget-heading .elementor-heading-title.elementor-size-medium {
                              font-size: 19px
                           }

                           .elementor-widget-heading .elementor-heading-title.elementor-size-large {
                              font-size: 29px
                           }

                           .elementor-widget-heading .elementor-heading-title.elementor-size-xl {
                              font-size: 39px
                           }

                           .elementor-widget-heading .elementor-heading-title.elementor-size-xxl {
                              font-size: 59px
                           }
                        </style>
                        <!-- <h1 class="elementor-heading-title elementor-size-default">{{$property_title}}</h1> -->
                     </div>
                  </div>

                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-20b240eb elementor-section-content-middle elementor-section-boxed elementor-section-height-default" data-id="20b240eb" data-element_type="section" style="background-color: transparent;">
                     <div class="elementor-container elementor-column-gap-default" style="justify-content: space-between;">
                        <div class="elementor-column elementor-col-20 elementor-inner-column elementor-element elementor-element-5e5d23c" data-id="5e5d23c" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-c61edaf elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="c61edaf" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-map-marker-alt" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <!-- <div class="elementor-icon-box-title">
                                             <span>
                                                Locations </span>
                                          </div> -->
                                          <p class="elementor-icon-box-description">
                                             {{$address}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="elementor-column elementor-col-20 elementor-inner-column elementor-element elementor-element-731e030c" data-id="731e030c" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-1cbe1306 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="1cbe1306" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <link rel="stylesheet" href="https://propertyinfo.gr/wp-content/plugins/elementor/assets/css/widget-icon-box.min.css">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-file-invoice-dollar" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-153 31V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zM64 72c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8V72zm0 80v-16c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8zm144 263.88V440c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-24.29c-11.29-.58-22.27-4.52-31.37-11.35-3.9-2.93-4.1-8.77-.57-12.14l11.75-11.21c2.77-2.64 6.89-2.76 10.13-.73 3.87 2.42 8.26 3.72 12.82 3.72h28.11c6.5 0 11.8-5.92 11.8-13.19 0-5.95-3.61-11.19-8.77-12.73l-45-13.5c-18.59-5.58-31.58-23.42-31.58-43.39 0-24.52 19.05-44.44 42.67-45.07V232c0-4.42 3.58-8 8-8h16c4.42 0 8 3.58 8 8v24.29c11.29.58 22.27 4.51 31.37 11.35 3.9 2.93 4.1 8.77.57 12.14l-11.75 11.21c-2.77 2.64-6.89 2.76-10.13.73-3.87-2.43-8.26-3.72-12.82-3.72h-28.11c-6.5 0-11.8 5.92-11.8 13.19 0 5.95 3.61 11.19 8.77 12.73l45 13.5c18.59 5.58 31.58 23.42 31.58 43.39 0 24.53-19.05 44.44-42.67 45.07z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Sale Price </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$sale_price}} €
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!-- <div class="elementor-element elementor-element-5060f032 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="5060f032" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-file-invoice-dollar" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-153 31V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zM64 72c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8V72zm0 80v-16c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8zm144 263.88V440c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-24.29c-11.29-.58-22.27-4.52-31.37-11.35-3.9-2.93-4.1-8.77-.57-12.14l11.75-11.21c2.77-2.64 6.89-2.76 10.13-.73 3.87 2.42 8.26 3.72 12.82 3.72h28.11c6.5 0 11.8-5.92 11.8-13.19 0-5.95-3.61-11.19-8.77-12.73l-45-13.5c-18.59-5.58-31.58-23.42-31.58-43.39 0-24.52 19.05-44.44 42.67-45.07V232c0-4.42 3.58-8 8-8h16c4.42 0 8 3.58 8 8v24.29c11.29.58 22.27 4.51 31.37 11.35 3.9 2.93 4.1 8.77.57 12.14l-11.75 11.21c-2.77 2.64-6.89 2.76-10.13.73-3.87-2.43-8.26-3.72-12.82-3.72h-28.11c-6.5 0-11.8 5.92-11.8 13.19 0 5.95 3.61 11.19 8.77 12.73l45 13.5c18.59 5.58 31.58 23.42 31.58 43.39 0 24.53-19.05 44.44-42.67 45.07z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Price for Sale </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             1000€
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div> -->
                           </div>
                        </div>
                     </div>
                  </section>
               </div>
            </div>
         </div>
      </section>
      <section class="elementor-section elementor-top-section elementor-element elementor-element-32eb9e60 rowshadwo elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="32eb9e60" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
         <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-8d51217" data-id="8d51217" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
               <div class="elementor-widget-wrap elementor-element-populated">
                  <div class="elementor-element elementor-element-34b6f1cc elementor-widget elementor-widget-heading" data-id="34b6f1cc" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <h4 class="elementor-heading-title elementor-size-default">Overview</h4>
                     </div>
                  </div>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-39e505b4 content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="39e505b4" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-16f8d7" data-id="16f8d7" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-eaa79c3 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="eaa79c3" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-chalkboard-teacher" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M208 352c-2.39 0-4.78.35-7.06 1.09C187.98 357.3 174.35 360 160 360c-14.35 0-27.98-2.7-40.95-6.91-2.28-.74-4.66-1.09-7.05-1.09C49.94 352-.33 402.48 0 464.62.14 490.88 21.73 512 48 512h224c26.27 0 47.86-21.12 48-47.38.33-62.14-49.94-112.62-112-112.62zm-48-32c53.02 0 96-42.98 96-96s-42.98-96-96-96-96 42.98-96 96 42.98 96 96 96zM592 0H208c-26.47 0-48 22.25-48 49.59V96c23.42 0 45.1 6.78 64 17.8V64h352v288h-64v-64H384v64h-76.24c19.1 16.69 33.12 38.73 39.69 64H592c26.47 0 48-22.25 48-49.59V49.59C640 22.25 618.47 0 592 0z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                ID </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             67894
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-522764f8" data-id="522764f8" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-7af5d5fe elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="7af5d5fe" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-map-marker-alt" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>Property Type</span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$propertyType}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-13898dd7" data-id="13898dd7" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-5c98d395 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="5c98d395" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Bedroom </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$bedrooms}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-1a2cc3d9" data-id="1a2cc3d9" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-676a3370 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="676a3370" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-bath" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M32,384a95.4,95.4,0,0,0,32,71.09V496a16,16,0,0,0,16,16h32a16,16,0,0,0,16-16V480H384v16a16,16,0,0,0,16,16h32a16,16,0,0,0,16-16V455.09A95.4,95.4,0,0,0,480,384V336H32ZM496,256H80V69.25a21.26,21.26,0,0,1,36.28-15l19.27,19.26c-13.13,29.88-7.61,59.11,8.62,79.73l-.17.17A16,16,0,0,0,144,176l11.31,11.31a16,16,0,0,0,22.63,0L283.31,81.94a16,16,0,0,0,0-22.63L272,48a16,16,0,0,0-22.62,0l-.17.17c-20.62-16.23-49.83-21.75-79.73-8.62L150.22,20.28A69.25,69.25,0,0,0,32,69.25V256H16A16,16,0,0,0,0,272v16a16,16,0,0,0,16,16H496a16,16,0,0,0,16-16V272A16,16,0,0,0,496,256Z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Bathroom </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$bathrooms}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-5eb6cf5a content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="5eb6cf5a" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-6b732a56" data-id="6b732a56" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-11be8096 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="11be8096" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Floorspace </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$floorspace}} {!! $floorspace_units == 'ft2' ? 'ft<sup>2</sup>' : 'm<sup>2</sup>' !!}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-3cdb71d4" data-id="3cdb71d4" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-5e40c201 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="5e40c201" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-street-view" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M367.9 329.76c-4.62 5.3-9.78 10.1-15.9 13.65v22.94c66.52 9.34 112 28.05 112 49.65 0 30.93-93.12 56-208 56S48 446.93 48 416c0-21.6 45.48-40.3 112-49.65v-22.94c-6.12-3.55-11.28-8.35-15.9-13.65C58.87 345.34 0 378.05 0 416c0 53.02 114.62 96 256 96s256-42.98 256-96c0-37.95-58.87-70.66-144.1-86.24zM256 128c35.35 0 64-28.65 64-64S291.35 0 256 0s-64 28.65-64 64 28.65 64 64 64zm-64 192v96c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-96c17.67 0 32-14.33 32-32v-96c0-26.51-21.49-48-48-48h-11.8c-11.07 5.03-23.26 8-36.2 8s-25.13-2.97-36.2-8H208c-26.51 0-48 21.49-48 48v96c0 17.67 14.33 32 32 32z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Sea View </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$see_view}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-27a12d9a" data-id="27a12d9a" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-29fd681c elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="29fd681c" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fab-simplybuilt" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M481.2 64h-106c-14.5 0-26.6 11.8-26.6 26.3v39.6H163.3V90.3c0-14.5-12-26.3-26.6-26.3h-106C16.1 64 4.3 75.8 4.3 90.3v331.4c0 14.5 11.8 26.3 26.6 26.3h450.4c14.8 0 26.6-11.8 26.6-26.3V90.3c-.2-14.5-12-26.3-26.7-26.3zM149.8 355.8c-36.6 0-66.4-29.7-66.4-66.4 0-36.9 29.7-66.6 66.4-66.6 36.9 0 66.6 29.7 66.6 66.6 0 36.7-29.7 66.4-66.6 66.4zm212.4 0c-36.9 0-66.6-29.7-66.6-66.6 0-36.6 29.7-66.4 66.6-66.4 36.6 0 66.4 29.7 66.4 66.4 0 36.9-29.8 66.6-66.4 66.6z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Year of Construction </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$yearOfConstruction}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-2bc7b408" data-id="2bc7b408" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-62dac479 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="62dac479" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-check-double" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M505 174.8l-39.6-39.6c-9.4-9.4-24.6-9.4-33.9 0L192 374.7 80.6 263.2c-9.4-9.4-24.6-9.4-33.9 0L7 302.9c-9.4 9.4-9.4 24.6 0 34L175 505c9.4 9.4 24.6 9.4 33.9 0l296-296.2c9.4-9.5 9.4-24.7.1-34zm-324.3 106c6.2 6.3 16.4 6.3 22.6 0l208-208.2c6.2-6.3 6.2-16.4 0-22.6L366.1 4.7c-6.2-6.3-16.4-6.3-22.6 0L192 156.2l-55.4-55.5c-6.2-6.3-16.4-6.3-22.6 0L68.7 146c-6.2 6.3-6.2 16.4 0 22.6l112 112.2z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Suitable For </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$suitable_for}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-44412687 content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="44412687" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <!-- <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-739156f6" data-id="739156f6" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-22507793 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="22507793" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Plot Area (Sqm) </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             125
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div> -->
                        @if($beach_distance != "")
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-7bd5f651" data-id="7bd5f651" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-1ff2832d elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="1ff2832d" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-umbrella-beach" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M115.38 136.9l102.11 37.18c35.19-81.54 86.21-144.29 139-173.7-95.88-4.89-188.78 36.96-248.53 111.8-6.69 8.4-2.66 21.05 7.42 24.72zm132.25 48.16l238.48 86.83c35.76-121.38 18.7-231.66-42.63-253.98-7.4-2.7-15.13-4-23.09-4-58.02.01-128.27 69.17-172.76 171.15zM521.48 60.5c6.22 16.3 10.83 34.6 13.2 55.19 5.74 49.89-1.42 108.23-18.95 166.98l102.62 37.36c10.09 3.67 21.31-3.43 21.57-14.17 2.32-95.69-41.91-187.44-118.44-245.36zM560 447.98H321.06L386 269.5l-60.14-21.9-72.9 200.37H16c-8.84 0-16 7.16-16 16.01v32.01C0 504.83 7.16 512 16 512h544c8.84 0 16-7.17 16-16.01v-32.01c0-8.84-7.16-16-16-16z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Beach Distane </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$beach_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                        <!-- <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-6e8a6c56" data-id="6e8a6c56" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-7a42738 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="7a42738" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-grip-horizontal" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M96 288H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm160 0h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm160 0h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zM96 96H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm160 0h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm160 0h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Orientation </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             Meridian
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div> -->
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-7c1d5146" data-id="7c1d5146" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-3abba0d6 elementor-position-left elementor-vertical-align-middle elementor-view-default elementor-mobile-position-top elementor-widget elementor-widget-icon-box" data-id="3abba0d6" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-icon">
                                          <span class="elementor-icon elementor-animation-">
                                             <svg aria-hidden="true" class="e-font-icon-svg e-fas-retweet" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M629.657 343.598L528.971 444.284c-9.373 9.372-24.568 9.372-33.941 0L394.343 343.598c-9.373-9.373-9.373-24.569 0-33.941l10.823-10.823c9.562-9.562 25.133-9.34 34.419.492L480 342.118V160H292.451a24.005 24.005 0 0 1-16.971-7.029l-16-16C244.361 121.851 255.069 96 276.451 96H520c13.255 0 24 10.745 24 24v222.118l40.416-42.792c9.285-9.831 24.856-10.054 34.419-.492l10.823 10.823c9.372 9.372 9.372 24.569-.001 33.941zm-265.138 15.431A23.999 23.999 0 0 0 347.548 352H160V169.881l40.416 42.792c9.286 9.831 24.856 10.054 34.419.491l10.822-10.822c9.373-9.373 9.373-24.569 0-33.941L144.971 67.716c-9.373-9.373-24.569-9.373-33.941 0L10.343 168.402c-9.373 9.373-9.373 24.569 0 33.941l10.822 10.822c9.562 9.562 25.133 9.34 34.419-.491L96 169.881V392c0 13.255 10.745 24 24 24h243.549c21.382 0 32.09-25.851 16.971-40.971l-16.001-16z"></path>
                                             </svg>
                                          </span>
                                       </div>
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Heating System </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$heating_system}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
               </div>
            </div>
            <div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-11b18cc5" data-id="11b18cc5" data-element_type="column">
               <div class="elementor-widget-wrap elementor-element-populated">
                  <div class="elementor-element elementor-element-3a657788 elementor-widget elementor-widget-google_maps" data-id="3a657788" data-element_type="widget" data-widget_type="google_maps.default">
                     <div class="elementor-widget-container">
                        <style>
                           /*! elementor - v3.13.0 - 08-05-2023 */
                           .elementor-widget-google_maps .elementor-widget-container {
                              overflow: hidden
                           }

                           .elementor-widget-google_maps .elementor-custom-embed {
                              line-height: 0
                           }

                           .elementor-widget-google_maps iframe {
                              height: 300px
                           }
                        </style>
                        <div class="elementor-custom-embed">
                           <!-- <iframe loading="lazy"
                              src="https://maps.google.com/maps?q=4QG7%2BMM%20Sithonia%2C%20Greece&#038;t=m&#038;z=14&#038;output=embed&#038;iwloc=near"
                              title="4QG7+MM Sithonia, Greece"
                              aria-label="4QG7+MM Sithonia, Greece"></iframe> -->
                              <div id="map" style="height: 500px; width: 100%;"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <section class="elementor-section elementor-top-section elementor-element elementor-element-1d27163f elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="1d27163f" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
         <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-66 elementor-top-column elementor-element elementor-element-1191e6f9" data-id="1191e6f9" data-element_type="column">
               <div class="elementor-widget-wrap elementor-element-populated">
                  <div class="elementor-element elementor-element-471815f9 e-con-boxed e-flex e-con" data-id="471815f9" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}">
                     <div class="e-con-inner">
                        <div class="elementor-element elementor-element-6bc10593 elementor-widget elementor-widget-heading" data-id="6bc10593" data-element_type="widget" data-widget_type="heading.default">
                           <div class="elementor-widget-container">
                              <h4 class="elementor-heading-title elementor-size-default">Price and Other Features</h4>
                           </div>
                        </div>
                     </div>
                  </div>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-3739937d content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="3739937d" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        @if($monthly_rates != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-33293a5b" data-id="33293a5b" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-48ba54a elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="48ba54a" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Price </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$monthly_rates}} €/Per Month
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($monthly_rates_sqm != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-eb3a340" data-id="eb3a340" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-648e7795 elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="648e7795" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Rent Price (per Sqm) </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$monthly_rates_sqm}} €/Per Month
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($sale_price != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-678c7be4" data-id="678c7be4" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-10216a15 elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="10216a15" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Sale Price </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$sale_price}} €
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                  </section>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-6f67f674 content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="6f67f674" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        @if($sale_price_sqm !="")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-118971a9" data-id="118971a9" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-3ca718fd elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="3ca718fd" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Sale Price (per sq m) </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$sale_price_sqm}} €
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($additiona_features != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-479e8d62" data-id="479e8d62" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-3e74dd02 elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="3e74dd02" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Additional Features </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$additiona_features}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($roi != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-c2dceb0" data-id="c2dceb0" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-57099b25 elementor-view-default elementor-vertical-align-top elementor-widget elementor-widget-icon-box" data-id="57099b25" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                ROI: ROI </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$roi}} €
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                  </section>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-6466a0a7 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="6466a0a7" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-450cadfe" data-id="450cadfe" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-20ab4998 elementor-widget elementor-widget-heading" data-id="20ab4998" data-element_type="widget" data-widget_type="heading.default">
                                 <div class="elementor-widget-container">
                                    <h4 class="elementor-heading-title elementor-size-default">Other Details</h4>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>

                  @if($marina_distance != "")
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-7d934e09 content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="7d934e09" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-2ac4db94" data-id="2ac4db94" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-7df953f3 elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="7df953f3" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>Marina Distance </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$marina_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($school_distance != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-679d18d9" data-id="679d18d9" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-4a178554 elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="4a178554" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                School Distance </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$school_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($entertainment_facility_distance != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-7ce1d1cd" data-id="7ce1d1cd" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-39bbe6ae elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="39bbe6ae" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Entertainment Facility Distance </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$entertainment_facility_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                  </section>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-772adf04 content-text elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="772adf04" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-3d122293" data-id="3d122293" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-7444c69e elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="7444c69e" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Year of Renovation </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$year_of_renovation}}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        @if($infrastructure_distance != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-2e48f5f7" data-id="2e48f5f7" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-64793448 elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="64793448" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Infrastructures Distance </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$infrastructure_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif

                        @if($airport_distance != "")
                        <div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-4b4225a" data-id="4b4225a" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-e98a57b elementor-vertical-align-middle elementor-view-default elementor-widget elementor-widget-icon-box" data-id="e98a57b" data-element_type="widget" data-widget_type="icon-box.default">
                                 <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                       <div class="elementor-icon-box-content">
                                          <div class="elementor-icon-box-title">
                                             <span>
                                                Airoport Distance </span>
                                          </div>
                                          <p class="elementor-icon-box-description">
                                             {{$airport_distance}} KM
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                  </section>
                  <div class="elementor-element elementor-element-162f3feb e-con-boxed e-flex e-con" data-id="162f3feb" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}">
                     <div class="e-con-inner">
                        <div class="elementor-element elementor-element-35083eaf elementor-widget elementor-widget-heading" data-id="35083eaf" data-element_type="widget" data-widget_type="heading.default">
                           <div class="elementor-widget-container">
                              <h4 class="elementor-heading-title elementor-size-default">{{$description_title}}</h4>
                           </div>
                        </div>
                        <div class="elementor-element elementor-element-6d2087de elementor-widget elementor-widget-text-editor" data-id="6d2087de" data-element_type="widget" data-widget_type="text-editor.default">
                           <div class="elementor-widget-container">
                              <style>
                                 /*! elementor - v3.13.0 - 08-05-2023 */
                                 .elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap {
                                    background-color: #69727d;
                                    color: #fff
                                 }

                                 .elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap {
                                    color: #69727d;
                                    border: 3px solid;
                                    background-color: transparent
                                 }

                                 .elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap {
                                    margin-top: 8px
                                 }

                                 .elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter {
                                    width: 1em;
                                    height: 1em
                                 }

                                 .elementor-widget-text-editor .elementor-drop-cap {
                                    float: left;
                                    text-align: center;
                                    line-height: 1;
                                    font-size: 50px
                                 }

                                 .elementor-widget-text-editor .elementor-drop-cap-letter {
                                    display: inline-block
                                 }
                              </style>
                              {{$description_summary}}
                           </div>
                        </div>
                        <div class="elementor-element elementor-element-227fec57 elementor-widget elementor-widget-toggle" data-id="227fec57" data-element_type="widget" data-widget_type="toggle.default">
                           <div class="elementor-widget-container">
                              <style>
                                 /*! elementor - v3.13.0 - 08-05-2023 */
                                 .elementor-toggle {
                                    text-align: left
                                 }

                                 .elementor-toggle .elementor-tab-title {
                                    font-weight: 700;
                                    line-height: 1;
                                    margin: 0;
                                    padding: 15px;
                                    border-bottom: 1px solid #d5d8dc;
                                    cursor: pointer;
                                    outline: none
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon {
                                    display: inline-block;
                                    width: 1em
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon svg {
                                    -webkit-margin-start: -5px;
                                    margin-inline-start: -5px;
                                    width: 1em;
                                    height: 1em
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon.elementor-toggle-icon-right {
                                    float: right;
                                    text-align: right
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon.elementor-toggle-icon-left {
                                    float: left;
                                    text-align: left
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon .elementor-toggle-icon-closed {
                                    display: block
                                 }

                                 .elementor-toggle .elementor-tab-title .elementor-toggle-icon .elementor-toggle-icon-opened {
                                    display: none
                                 }

                                 .elementor-toggle .elementor-tab-title.elementor-active {
                                    border-bottom: none
                                 }

                                 .elementor-toggle .elementor-tab-title.elementor-active .elementor-toggle-icon-closed {
                                    display: none
                                 }

                                 .elementor-toggle .elementor-tab-title.elementor-active .elementor-toggle-icon-opened {
                                    display: block
                                 }

                                 .elementor-toggle .elementor-tab-content {
                                    padding: 15px;
                                    border-bottom: 1px solid #d5d8dc;
                                    display: none
                                 }

                                 @media (max-width:767px) {
                                    .elementor-toggle .elementor-tab-title {
                                       padding: 12px
                                    }

                                    .elementor-toggle .elementor-tab-content {
                                       padding: 12px 10px
                                    }
                                 }

                                 .e-con-inner>.elementor-widget-toggle,
                                 .e-con>.elementor-widget-toggle {
                                    width: var(--container-widget-width);
                                    --flex-grow: var(--container-widget-flex-grow)
                                 }
                              </style>
                              <div class="elementor-toggle" role="tablist">
                                 <div class="elementor-toggle-item">
                                    <div id="elementor-tab-title-5781" class="elementor-tab-title" data-tab="1" role="tab" aria-controls="elementor-tab-content-5781" aria-expanded="false">
                                       <span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
                                          <span class="elementor-toggle-icon-closed">
                                             <svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path>
                                             </svg>
                                          </span>
                                          <span class="elementor-toggle-icon-opened">
                                             <svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path>
                                             </svg>
                                          </span>
                                       </span>
                                       <a href="" class="elementor-toggle-title">Detail Description</a>
                                    </div>
                                    <div id="elementor-tab-content-5781" class="elementor-tab-content elementor-clearfix" data-tab="1" role="tabpanel" aria-labelledby="elementor-tab-title-5781">
                                       {!! $description !!}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- <div class="elementor-element elementor-element-75a564c0 elementor-widget elementor-widget-heading" data-id="75a564c0" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <h4 class="elementor-heading-title elementor-size-default">Exterior Features</h4>
                     </div>
                  </div>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-4db88b69 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="4db88b69" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-62de8066" data-id="62de8066" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-1a97a07a elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="1a97a07a" data-element_type="widget" data-widget_type="icon-list.default">
                                 <div class="elementor-widget-container">
                                    <link rel="stylesheet" href="https://propertyinfo.gr/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css">
                                    <ul class="elementor-icon-list-items">
                                       <li class="elementor-icon-list-item">
                                          <span class="elementor-icon-list-text">Residential zone</span>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section> -->
                  @if($amenities != "")
                  <div class="elementor-element elementor-element-4393cd27 elementor-widget elementor-widget-heading" data-id="4393cd27" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <h4 class="elementor-heading-title elementor-size-default">Amenities </h4>
                     </div>
                  </div>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-161dcb1b elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="161dcb1b" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-66c08550" data-id="66c08550" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-10bb9496 elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="10bb9496" data-element_type="widget" data-widget_type="icon-list.default">
                                 <div class="elementor-widget-container">
                                    <ul class="elementor-icon-list-items">
                                       <li class="elementor-icon-list-item">
                                          <span class="elementor-icon-list-text">{{$amenities}}</span>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  @endif
                  @if($kitchens != "")
                  <div class="elementor-element elementor-element-4393cd27 elementor-widget elementor-widget-heading" data-id="4393cd27" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <h4 class="elementor-heading-title elementor-size-default">Kitchens </h4>
                     </div>
                  </div>
                  <section class="elementor-section elementor-inner-section elementor-element elementor-element-161dcb1b elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="161dcb1b" data-element_type="section">
                     <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-66c08550" data-id="66c08550" data-element_type="column">
                           <div class="elementor-widget-wrap elementor-element-populated">
                              <div class="elementor-element elementor-element-10bb9496 elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="10bb9496" data-element_type="widget" data-widget_type="icon-list.default">
                                 <div class="elementor-widget-container">
                                    <ul class="elementor-icon-list-items">
                                       <li class="elementor-icon-list-item">
                                          <span class="elementor-icon-list-text">{{$kitchens}}</span>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  @endif
                  <div class="elementor-element elementor-element-68926b08 elementor-skin-slideshow elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile elementor-arrows-yes elementor-widget elementor-widget-media-carousel" data-id="68926b08" data-element_type="widget" data-settings="{&quot;skin&quot;:&quot;slideshow&quot;,&quot;effect&quot;:&quot;fade&quot;,&quot;centered_slides&quot;:&quot;yes&quot;,&quot;show_arrows&quot;:&quot;yes&quot;,&quot;speed&quot;:500,&quot;autoplay&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;loop&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;space_between&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:10,&quot;sizes&quot;:[]},&quot;space_between_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:10,&quot;sizes&quot;:[]},&quot;space_between_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:10,&quot;sizes&quot;:[]}}" data-widget_type="media-carousel.default">
                     <div class="elementor-widget-container">
                        <link rel="stylesheet" href="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/css/widget-carousel.min.css">
                        <div class="elementor-swiper">
                           <div class="elementor-main-swiper swiper-container">
                              <div class="swiper-wrapper">
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-room-at-home.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-room-at-home.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-home-interior.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-home-interior.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="luxury-and-modern-home-interior-with-design-green-sofa-1-1.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/luxury-and-modern-home-interior-with-design-green-sofa-1-1.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-house-exterior.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-house-exterior.jpg&#039;)">
                                    </div>
                                 </div>
                              </div>
                              <div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0">
                                 <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-left" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"></path>
                                 </svg>
                                 <span class="elementor-screen-only">Previous</span>
                              </div>
                              <div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0">
                                 <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-right" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"></path>
                                 </svg>
                                 <span class="elementor-screen-only">Next</span>
                              </div>
                           </div>
                        </div>
                        <div class="elementor-swiper">
                           <div class="elementor-thumbnails-swiper swiper-container">
                              <div class="swiper-wrapper">
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-room-at-home.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-room-at-home.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-home-interior.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-home-interior.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="luxury-and-modern-home-interior-with-design-green-sofa-1-1.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/luxury-and-modern-home-interior-with-design-green-sofa-1-1.jpg&#039;)">
                                    </div>
                                 </div>
                                 <div class="swiper-slide">
                                    <div class="elementor-carousel-image" role="img" aria-label="modern-house-exterior.jpg" style="background-image: url(&#039;https://propertyinfo.gr/wp-content/uploads/2023/05/modern-house-exterior.jpg&#039;)">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="elementor-element elementor-element-5e27afc2 elementor-widget elementor-widget-heading" data-id="5e27afc2" data-element_type="widget" data-widget_type="heading.default">
                     <div class="elementor-widget-container">
                        <h4 class="elementor-heading-title elementor-size-default">Floor Plan</h4>
                     </div>
                  </div>
                  <div class="elementor-element elementor-element-1ef39770 elementor-widget elementor-widget-image" data-id="1ef39770" data-element_type="widget" data-widget_type="image.default">
                     <div class="elementor-widget-container">
                        <style>
                           /*! elementor - v3.13.0 - 08-05-2023 */
                           .elementor-widget-image {
                              text-align: center
                           }

                           .elementor-widget-image a {
                              display: inline-block
                           }

                           .elementor-widget-image a img[src$=".svg"] {
                              width: 48px
                           }

                           .elementor-widget-image img {
                              vertical-align: middle;
                              display: inline-block
                           }
                        </style>
                        @foreach ($finalFloorPlanImages as $image)
                        <img width="1024" height="682" src="{{ $image }}" class="attachment-large size-large wp-image-15112" alt="" style="margin-bottom: 20px" />
                        @endforeach
                     </div>

                  </div>
               </div>
            </div>
            <!-- <div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-564c656a" data-id="564c656a" data-element_type="column">
                  <div class="elementor-widget-wrap elementor-element-populated">
                     <section class="elementor-section elementor-inner-section elementor-element elementor-element-5404161f elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="5404161f" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;sticky&quot;:&quot;top&quot;,&quot;sticky_offset&quot;:100,&quot;sticky_parent&quot;:&quot;yes&quot;,&quot;sticky_on&quot;:[&quot;desktop&quot;,&quot;tablet&quot;,&quot;mobile&quot;],&quot;sticky_effects_offset&quot;:0}">
                        <div class="elementor-container elementor-column-gap-default">
                           <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-53d21157" data-id="53d21157" data-element_type="column">
                              <div class="elementor-widget-wrap elementor-element-populated">
                                 <div class="elementor-element elementor-element-5b000592 elementor-widget elementor-widget-heading" data-id="5b000592" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                       <h4 class="elementor-heading-title elementor-size-default">Contact Agent</h4>
                                    </div>
                                 </div>
                                 <div class="elementor-element elementor-element-2f9c4422 elementor-position-left elementor-vertical-align-top elementor-widget elementor-widget-image-box" data-id="2f9c4422" data-element_type="widget" data-widget_type="image-box.default">
                                    <div class="elementor-widget-container">
                                       <style>/*! elementor - v3.13.0 - 08-05-2023 */
                                          .elementor-widget-image-box .elementor-image-box-content{width:100%}@media (min-width:768px){.elementor-widget-image-box.elementor-position-left .elementor-image-box-wrapper,.elementor-widget-image-box.elementor-position-right .elementor-image-box-wrapper{display:flex}.elementor-widget-image-box.elementor-position-right .elementor-image-box-wrapper{text-align:right;flex-direction:row-reverse}.elementor-widget-image-box.elementor-position-left .elementor-image-box-wrapper{text-align:left;flex-direction:row}.elementor-widget-image-box.elementor-position-top .elementor-image-box-img{margin:auto}.elementor-widget-image-box.elementor-vertical-align-top .elementor-image-box-wrapper{align-items:flex-start}.elementor-widget-image-box.elementor-vertical-align-middle .elementor-image-box-wrapper{align-items:center}.elementor-widget-image-box.elementor-vertical-align-bottom .elementor-image-box-wrapper{align-items:flex-end}}@media (max-width:767px){.elementor-widget-image-box .elementor-image-box-img{margin-left:auto!important;margin-right:auto!important;margin-bottom:15px}}.elementor-widget-image-box .elementor-image-box-img{display:inline-block}.elementor-widget-image-box .elementor-image-box-title a{color:inherit}.elementor-widget-image-box .elementor-image-box-wrapper{text-align:center}.elementor-widget-image-box .elementor-image-box-description{margin:0}
                                       </style>
                                       <div class="elementor-image-box-wrapper"></div>
                                    </div>
                                 </div>
                                 <div class="elementor-element elementor-element-620eaa0e e-grid-align-left e-grid-align-mobile-center upersocial elementor-shape-rounded elementor-grid-0 elementor-widget elementor-widget-social-icons" data-id="620eaa0e" data-element_type="widget" data-widget_type="social-icons.default">
                                    <div class="elementor-widget-container">
                                       <style>/*! elementor - v3.13.0 - 08-05-2023 */
                                          .elementor-widget-social-icons.elementor-grid-0 .elementor-widget-container,.elementor-widget-social-icons.elementor-grid-mobile-0 .elementor-widget-container,.elementor-widget-social-icons.elementor-grid-tablet-0 .elementor-widget-container{line-height:1;font-size:0}.elementor-widget-social-icons:not(.elementor-grid-0):not(.elementor-grid-tablet-0):not(.elementor-grid-mobile-0) .elementor-grid{display:inline-grid}.elementor-widget-social-icons .elementor-grid{grid-column-gap:var(--grid-column-gap,5px);grid-row-gap:var(--grid-row-gap,5px);grid-template-columns:var(--grid-template-columns);justify-content:var(--justify-content,center);justify-items:var(--justify-content,center)}.elementor-icon.elementor-social-icon{font-size:var(--icon-size,25px);line-height:var(--icon-size,25px);width:calc(var(--icon-size, 25px) + (2 * var(--icon-padding, .5em)));height:calc(var(--icon-size, 25px) + (2 * var(--icon-padding, .5em)))}.elementor-social-icon{--e-social-icon-icon-color:#fff;display:inline-flex;background-color:#69727d;align-items:center;justify-content:center;text-align:center;cursor:pointer}.elementor-social-icon i{color:var(--e-social-icon-icon-color)}.elementor-social-icon svg{fill:var(--e-social-icon-icon-color)}.elementor-social-icon:last-child{margin:0}.elementor-social-icon:hover{opacity:.9;color:#fff}.elementor-social-icon-android{background-color:#a4c639}.elementor-social-icon-apple{background-color:#999}.elementor-social-icon-behance{background-color:#1769ff}.elementor-social-icon-bitbucket{background-color:#205081}.elementor-social-icon-codepen{background-color:#000}.elementor-social-icon-delicious{background-color:#39f}.elementor-social-icon-deviantart{background-color:#05cc47}.elementor-social-icon-digg{background-color:#005be2}.elementor-social-icon-dribbble{background-color:#ea4c89}.elementor-social-icon-elementor{background-color:#d30c5c}.elementor-social-icon-envelope{background-color:#ea4335}.elementor-social-icon-facebook,.elementor-social-icon-facebook-f{background-color:#3b5998}.elementor-social-icon-flickr{background-color:#0063dc}.elementor-social-icon-foursquare{background-color:#2d5be3}.elementor-social-icon-free-code-camp,.elementor-social-icon-freecodecamp{background-color:#006400}.elementor-social-icon-github{background-color:#333}.elementor-social-icon-gitlab{background-color:#e24329}.elementor-social-icon-globe{background-color:#69727d}.elementor-social-icon-google-plus,.elementor-social-icon-google-plus-g{background-color:#dd4b39}.elementor-social-icon-houzz{background-color:#7ac142}.elementor-social-icon-instagram{background-color:#262626}.elementor-social-icon-jsfiddle{background-color:#487aa2}.elementor-social-icon-link{background-color:#818a91}.elementor-social-icon-linkedin,.elementor-social-icon-linkedin-in{background-color:#0077b5}.elementor-social-icon-medium{background-color:#00ab6b}.elementor-social-icon-meetup{background-color:#ec1c40}.elementor-social-icon-mixcloud{background-color:#273a4b}.elementor-social-icon-odnoklassniki{background-color:#f4731c}.elementor-social-icon-pinterest{background-color:#bd081c}.elementor-social-icon-product-hunt{background-color:#da552f}.elementor-social-icon-reddit{background-color:#ff4500}.elementor-social-icon-rss{background-color:#f26522}.elementor-social-icon-shopping-cart{background-color:#4caf50}.elementor-social-icon-skype{background-color:#00aff0}.elementor-social-icon-slideshare{background-color:#0077b5}.elementor-social-icon-snapchat{background-color:#fffc00}.elementor-social-icon-soundcloud{background-color:#f80}.elementor-social-icon-spotify{background-color:#2ebd59}.elementor-social-icon-stack-overflow{background-color:#fe7a15}.elementor-social-icon-steam{background-color:#00adee}.elementor-social-icon-stumbleupon{background-color:#eb4924}.elementor-social-icon-telegram{background-color:#2ca5e0}.elementor-social-icon-thumb-tack{background-color:#1aa1d8}.elementor-social-icon-tripadvisor{background-color:#589442}.elementor-social-icon-tumblr{background-color:#35465c}.elementor-social-icon-twitch{background-color:#6441a5}.elementor-social-icon-twitter{background-color:#1da1f2}.elementor-social-icon-viber{background-color:#665cac}.elementor-social-icon-vimeo{background-color:#1ab7ea}.elementor-social-icon-vk{background-color:#45668e}.elementor-social-icon-weibo{background-color:#dd2430}.elementor-social-icon-weixin{background-color:#31a918}.elementor-social-icon-whatsapp{background-color:#25d366}.elementor-social-icon-wordpress{background-color:#21759b}.elementor-social-icon-xing{background-color:#026466}.elementor-social-icon-yelp{background-color:#af0606}.elementor-social-icon-youtube{background-color:#cd201f}.elementor-social-icon-500px{background-color:#0099e5}.elementor-shape-rounded .elementor-icon.elementor-social-icon{border-radius:10%}.elementor-shape-circle .elementor-icon.elementor-social-icon{border-radius:50%}
                                       </style>
                                       <div class="elementor-social-icons-wrapper elementor-grid">
                                          <span class="elementor-grid-item">
                                             <a class="elementor-icon elementor-social-icon elementor-social-icon-envelope elementor-repeater-item-a25c116" target="_blank">
                                                <span class="elementor-screen-only">Envelope</span>
                                                <svg class="e-font-icon-svg e-far-envelope" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                   <path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
                                                </svg>
                                             </a>
                                          </span>
                                          <span class="elementor-grid-item">
                                             <a class="elementor-icon elementor-social-icon elementor-social-icon-globe elementor-repeater-item-ad3aad1" target="_blank">
                                                <span class="elementor-screen-only">Globe</span>
                                                <svg class="e-font-icon-svg e-fas-globe" viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg">
                                                   <path d="M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z"></path>
                                                </svg>
                                             </a>
                                          </span>
                                          <span class="elementor-grid-item">
                                             <a class="elementor-icon elementor-social-icon elementor-social-icon-facebook elementor-repeater-item-ad821cc" target="_blank">
                                                <span class="elementor-screen-only">Facebook</span>
                                                <svg class="e-font-icon-svg e-fab-facebook" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                   <path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"></path>
                                                </svg>
                                             </a>
                                          </span>
                                          <span class="elementor-grid-item">
                                             <a class="elementor-icon elementor-social-icon elementor-social-icon-instagram elementor-repeater-item-0f1cb11" target="_blank">
                                                <span class="elementor-screen-only">Instagram</span>
                                                <svg class="e-font-icon-svg e-fab-instagram" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                                   <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path>
                                                </svg>
                                             </a>
                                          </span>
                                          <span class="elementor-grid-item">
                                             <a class="elementor-icon elementor-social-icon elementor-social-icon-linkedin elementor-repeater-item-d89af08" target="_blank">
                                                <span class="elementor-screen-only">Linkedin</span>
                                                <svg class="e-font-icon-svg e-fab-linkedin" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                                   <path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path>
                                                </svg>
                                             </a>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="elementor-element elementor-element-15139d28 elementor-widget elementor-widget-text-editor" data-id="15139d28" data-element_type="widget" data-widget_type="text-editor.default">
                                    <div class="elementor-widget-container">
                                       <div class="dwnsocial">
                                          <a class="textnumber" href="tel:tel:"><i class="fa fa-phone-square" style="font-size:38px;color: #507496;"></i></a>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="elementor-element elementor-element-3af34f9c elementor-widget elementor-widget-heading" data-id="3af34f9c" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                       <h4 class="elementor-heading-title elementor-size-default">Request Info</h4>
                                    </div>
                                 </div>
                                 <div class="elementor-element elementor-element-15f5fb2a elementor-widget elementor-widget-shortcode" data-id="15f5fb2a" data-element_type="widget" data-widget_type="shortcode.default">
                                    <div class="elementor-widget-container">
                                       <div class="elementor-shortcode">
                                          <script type="text/javascript"></script>
                                          <div class='gf_browser_chrome gform_wrapper gravity-theme gform-theme--no-framework' data-form-theme='gravity-theme' data-form-index='0' id='gform_wrapper_9' style='display:none'>
                                             <div class='gform_heading'>
                                                <p class='gform_description'></p>
                                             </div>
                                             <form method='post' enctype='multipart/form-data'  id='gform_9'  action='/property/11754/' data-formid='9' novalidate>
                                                <div class='gform-body gform_body'>
                                                   <div id='gform_fields_9' class='gform_fields top_label form_sublabel_above description_below'>
                                                      <div id="field_9_7"  class="gfield gfield--type-section gsection field_sublabel_above gfield--no-description field_description_below gfield_visibility_hidden"  data-js-reload="field_9_7">
                                                         <div class='admin-hidden-markup'><i class='gform-icon gform-icon--hidden'></i><span>Hidden</span></div>
                                                         <h3 class="gsection_title">About You</h3>
                                                      </div>
                                                      <fieldset id="field_9_1"  class="gfield gfield--type-name gfield--width-full gfield_contains_required field_sublabel_above gfield--no-description field_description_below gfield_visibility_visible"  data-js-reload="field_9_1">
                                                         <legend class='gfield_label gform-field-label gfield_label_before_complex'  >Name<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></legend>
                                                         <div class='ginput_complex ginput_container ginput_container--name no_prefix has_first_name no_middle_name has_last_name no_suffix gf_name_has_2 ginput_container_name gform-grid-row' id='input_9_1'>
                                                            <span id='input_9_1_3_container' class='name_first gform-grid-col gform-grid-col--size-auto' >
                                                            <label for='input_9_1_3' class='gform-field-label gform-field-label--type-sub '>First</label>
                                                            <input type='text' name='input_1.3' id='input_9_1_3' value=''   aria-required='true'    autocomplete="given-name" />
                                                            </span>
                                                            <span id='input_9_1_6_container' class='name_last gform-grid-col gform-grid-col--size-auto' >
                                                            <label for='input_9_1_6' class='gform-field-label gform-field-label--type-sub '>Last</label>
                                                            <input type='text' name='input_1.6' id='input_9_1_6' value=''   aria-required='true'    autocomplete="family-name" />
                                                            </span>
                                                         </div>
                                                      </fieldset>
                                                      <fieldset id="field_9_4"  class="gfield gfield--type-address field_sublabel_above gfield--no-description field_description_below gfield_visibility_hidden"  data-js-reload="field_9_4">
                                                         <div class='admin-hidden-markup'><i class='gform-icon gform-icon--hidden'></i><span>Hidden</span></div>
                                                         <legend class='gfield_label gform-field-label gfield_label_before_complex'  >Your Address</legend>
                                                         <div class='ginput_complex ginput_container has_street has_street2 has_city has_zip has_country ginput_container_address gform-grid-row' id='input_9_4' >
                                                            <span class='ginput_full address_line_1 ginput_address_line_1 gform-grid-col' id='input_9_4_1_container' >
                                                            <label for='input_9_4_1' id='input_9_4_1_label' class='gform-field-label gform-field-label--type-sub '>Street Address</label>
                                                            <input type='text' name='input_4.1' id='input_9_4_1' value=''    aria-required='false'   autocomplete="address-line1" />
                                                            </span><span class='ginput_full address_line_2 ginput_address_line_2 gform-grid-col' id='input_9_4_2_container' >
                                                            <label for='input_9_4_2' id='input_9_4_2_label' class='gform-field-label gform-field-label--type-sub '>Address Line 2</label>
                                                            <input type='text' name='input_4.2' id='input_9_4_2' value=''    autocomplete="address-line2" aria-required='false'   />
                                                            </span><span class='ginput_left address_city ginput_address_city gform-grid-col' id='input_9_4_3_container' >
                                                            <label for='input_9_4_3' id='input_9_4_3_label' class='gform-field-label gform-field-label--type-sub '>City</label>
                                                            <input type='text' name='input_4.3' id='input_9_4_3' value=''    aria-required='false'   autocomplete="address-level2" />
                                                            </span><input type='hidden' class='gform_hidden' name='input_4.4' id='input_9_4_4' value='Virginia'/><span class='ginput_right address_zip ginput_address_zip gform-grid-col' id='input_9_4_5_container' >
                                                            <label for='input_9_4_5' id='input_9_4_5_label' class='gform-field-label gform-field-label--type-sub '>ZIP Code</label>
                                                            <input type='text' name='input_4.5' id='input_9_4_5' value=''    aria-required='false'   autocomplete="postal-code" />
                                                            </span><input type='hidden' class='gform_hidden' name='input_4.6' id='input_9_4_6' value='United States' />
                                                            <div class='gf_clear gf_clear_complex'></div>
                                                         </div>
                                                      </fieldset>
                                                      <div id="field_9_10"  class="gfield gfield--type-section gsection field_sublabel_above gfield--has-description field_description_below gfield_visibility_hidden"  data-js-reload="field_9_10">
                                                         <div class='admin-hidden-markup'><i class='gform-icon gform-icon--hidden'></i><span>Hidden</span></div>
                                                         <h3 class="gsection_title">How Can We Reach You?</h3>
                                                         <div class='gsection_description' id='gfield_description_9_10'>We would love to chat with you. How can we get in touch?</div>
                                                      </div>
                                                      <div id="field_9_11"  class="gfield gfield--type-select field_sublabel_above gfield--no-description field_description_below gfield_visibility_visible"  data-js-reload="field_9_11">
                                                         <label class='gfield_label gform-field-label' for='input_9_11' >Preferred Method of Contact</label>
                                                         <div class='ginput_container ginput_container_select'>
                                                            <select name='input_11' id='input_9_11' class='large gfield_select'     aria-invalid="false" >
                                                               <option value='Email' >Email</option>
                                                               <option value='Phone' >Phone</option>
                                                            </select>
                                                         </div>
                                                      </div>
                                                      <fieldset id="field_9_2"  class="gfield gfield--type-email gfield_contains_required field_sublabel_above gfield--no-description field_description_below gfield_visibility_visible"  data-js-reload="field_9_2">
                                                         <legend class='gfield_label gform-field-label gfield_label_before_complex'  >Your Email Address<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></legend>
                                                         <div class='ginput_complex ginput_container ginput_container_email gform-grid-row' id='input_9_2_container'>
                                                            <span id='input_9_2_1_container' class='ginput_left gform-grid-col gform-grid-col--size-auto'>
                                                            <label for='input_9_2' class='gform-field-label gform-field-label--type-sub '>Your email address</label>
                                                            <input class='' type='email' name='input_2' id='input_9_2' value=''    aria-required="true" aria-invalid="false"  autocomplete="email"/>
                                                            </span>
                                                            <span id='input_9_2_2_container' class='ginput_right gform-grid-col gform-grid-col--size-auto'>
                                                            <label for='input_9_2_2' class='gform-field-label gform-field-label--type-sub '>Confirm email address</label>
                                                            <input class='' type='email' name='input_2_2' id='input_9_2_2' value=''    aria-required="true" aria-invalid="false"  autocomplete="email"/>
                                                            </span>
                                                            <div class='gf_clear gf_clear_complex'></div>
                                                         </div>
                                                      </fieldset>
                                                      <div id="field_9_5"  class="gfield gfield--type-phone gfield_contains_required field_sublabel_above gfield--no-description field_description_below gfield_visibility_visible"  data-js-reload="field_9_5">
                                                         <label class='gfield_label gform-field-label' for='input_9_5' >Your Phone<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></label>
                                                         <div class='ginput_container ginput_container_phone'><input name='input_5' id='input_9_5' type='tel' value='' class='medium'   aria-required="true" aria-invalid="false"  autocomplete="tel" /></div>
                                                      </div>
                                                      <div id="field_9_12"  class="gfield gfield--type-select gfield_contains_required field_sublabel_above gfield--no-description field_description_below gfield_visibility_hidden"  data-js-reload="field_9_12">
                                                         <div class='admin-hidden-markup'><i class='gform-icon gform-icon--hidden'></i><span>Hidden</span></div>
                                                         <label class='gfield_label gform-field-label' for='input_9_12' >Best Time to Call You<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></label>
                                                         <div class='ginput_container ginput_container_select'>
                                                            <select name='input_12' id='input_9_12' class='medium gfield_select'    aria-required="true" aria-invalid="false" >
                                                               <option value='' selected='selected'>Select A Time</option>
                                                               <option value='12:00 am' >12:00 am</option>
                                                               <option value='12:30 am' >12:30 am</option>
                                                               <option value='1:00 am' >1:00 am</option>
                                                               <option value='1:30 am' >1:30 am</option>
                                                               <option value='2:00 am' >2:00 am</option>
                                                               <option value='2:30 am' >2:30 am</option>
                                                               <option value='3:00 am' >3:00 am</option>
                                                               <option value='3:30 am' >3:30 am</option>
                                                               <option value='4:00 am' >4:00 am</option>
                                                               <option value='4:30 am' >4:30 am</option>
                                                               <option value='5:00 am' >5:00 am</option>
                                                               <option value='5:30 am' >5:30 am</option>
                                                               <option value='6:00 am' >6:00 am</option>
                                                               <option value='6:30 am' >6:30 am</option>
                                                               <option value='7:00 am' >7:00 am</option>
                                                               <option value='7:30 am' >7:30 am</option>
                                                               <option value='8:00 am' >8:00 am</option>
                                                               <option value='8:30 am' >8:30 am</option>
                                                               <option value='9:00 am' >9:00 am</option>
                                                               <option value='9:30 am' >9:30 am</option>
                                                               <option value='10:00 am' >10:00 am</option>
                                                               <option value='10:30 am' >10:30 am</option>
                                                               <option value='11:00 am' >11:00 am</option>
                                                               <option value='11:30 am' >11:30 am</option>
                                                               <option value='12:00 pm' >12:00 pm</option>
                                                               <option value='12:30 pm' >12:30 pm</option>
                                                               <option value='1:00 pm' >1:00 pm</option>
                                                               <option value='1:30 pm' >1:30 pm</option>
                                                               <option value='2:00 pm' >2:00 pm</option>
                                                               <option value='2:30 pm' >2:30 pm</option>
                                                               <option value='3:00 pm' >3:00 pm</option>
                                                               <option value='3:30 pm' >3:30 pm</option>
                                                               <option value='4:00 pm' >4:00 pm</option>
                                                               <option value='4:30 pm' >4:30 pm</option>
                                                               <option value='5:00 pm' >5:00 pm</option>
                                                               <option value='5:30 pm' >5:30 pm</option>
                                                               <option value='6:00 pm' >6:00 pm</option>
                                                               <option value='6:30 pm' >6:30 pm</option>
                                                               <option value='7:00 pm' >7:00 pm</option>
                                                               <option value='7:30 pm' >7:30 pm</option>
                                                               <option value='8:00 pm' >8:00 pm</option>
                                                               <option value='8:30 pm' >8:30 pm</option>
                                                               <option value='9:00 pm' >9:00 pm</option>
                                                               <option value='9:30 pm' >9:30 pm</option>
                                                               <option value='10:00 pm' >10:00 pm</option>
                                                               <option value='10:30 pm' >10:30 pm</option>
                                                               <option value='11:00 pm' >11:00 pm</option>
                                                               <option value='11:30 pm' >11:30 pm</option>
                                                            </select>
                                                         </div>
                                                      </div>
                                                      <div id="field_9_8"  class="gfield gfield--type-section gsection field_sublabel_above gfield--has-description field_description_below gfield_visibility_hidden"  data-js-reload="field_9_8">
                                                         <div class='admin-hidden-markup'><i class='gform-icon gform-icon--hidden'></i><span>Hidden</span></div>
                                                         <h3 class="gsection_title">What&#039;s on your mind?</h3>
                                                         <div class='gsection_description' id='gfield_description_9_8'>Please let us know what&#039;s on your mind. Have a question for us? Ask away.</div>
                                                      </div>
                                                      <div id="field_9_3"  class="gfield gfield--type-textarea gfield_contains_required field_sublabel_above gfield--has-description field_description_below gfield_visibility_visible"  data-js-reload="field_9_3">
                                                         <label class='gfield_label gform-field-label' for='input_9_3' >Your message or inquiry<span class="gfield_required"><span class="gfield_required gfield_required_text">(Required)</span></span></label>
                                                         <div class='ginput_container ginput_container_textarea'><textarea name='input_3' id='input_9_3' class='textarea medium'  aria-describedby="gfield_description_9_3"   aria-required="true" aria-invalid="false"   rows='10' cols='50'></textarea></div>
                                                         <div class='gfield_description' id='gfield_description_9_3'>Enter your feedback, questions, or comments here</div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class='gform_footer top_label'> <input type='submit' id='gform_submit_button_9' class='gform_button button' value='Submit'  onclick='if(window["gf_submitting_9"]){return false;}  if( !jQuery("#gform_9")[0].checkValidity || jQuery("#gform_9")[0].checkValidity()){window["gf_submitting_9"]=true;}  ' onkeypress='if( event.keyCode == 13 ){ if(window["gf_submitting_9"]){return false;} if( !jQuery("#gform_9")[0].checkValidity || jQuery("#gform_9")[0].checkValidity()){window["gf_submitting_9"]=true;}  jQuery("#gform_9").trigger("submit",[true]); }' /> 
                                                   <input type='hidden' class='gform_hidden' name='is_submit_9' value='1' />
                                                   <input type='hidden' class='gform_hidden' name='gform_submit' value='9' />
                                                   <input type='hidden' class='gform_hidden' name='gform_unique_id' value='' />
                                                   <input type='hidden' class='gform_hidden' name='state_9' value='WyJbXSIsIjBhZmZkYzdhZWE2YWNiODlhNjU3MTE1YzllYTY4YThmIl0=' />
                                                   <input type='hidden' class='gform_hidden' name='gform_target_page_number_9' id='gform_target_page_number_9' value='0' />
                                                   <input type='hidden' class='gform_hidden' name='gform_source_page_number_9' id='gform_source_page_number_9' value='1' />
                                                   <input type='hidden' name='gform_field_values' value='' />
                                                </div>
                                             </form>
                                          </div>
                                          <script>
                                             gform.initializeOnLoaded( function() {gformInitSpinner( 9, 'https://propertyinfo.gr/wp-content/plugins/gravityforms/images/spinner.svg', true );jQuery('#gform_ajax_frame_9').on('load',function(){var contents = jQuery(this).contents().find('*').html();var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;if(!is_postback){return;}var form_content = jQuery(this).contents().find('#gform_wrapper_9');var is_confirmation = jQuery(this).contents().find('#gform_confirmation_wrapper_9').length > 0;var is_redirect = contents.indexOf('gformRedirect(){') >= 0;var is_form = form_content.length > 0 && ! is_redirect && ! is_confirmation;var mt = parseInt(jQuery('html').css('margin-top'), 10) + parseInt(jQuery('body').css('margin-top'), 10) + 100;if(is_form){jQuery('#gform_wrapper_9').html(form_content.html());if(form_content.hasClass('gform_validation_error')){jQuery('#gform_wrapper_9').addClass('gform_validation_error');} else {jQuery('#gform_wrapper_9').removeClass('gform_validation_error');}setTimeout( function() { /* delay the scroll by 50 milliseconds to fix a bug in chrome */  }, 50 );if(window['gformInitDatepicker']) {gformInitDatepicker();}if(window['gformInitPriceFields']) {gformInitPriceFields();}var current_page = jQuery('#gform_source_page_number_9').val();gformInitSpinner( 9, 'https://propertyinfo.gr/wp-content/plugins/gravityforms/images/spinner.svg', true );jQuery(document).trigger('gform_page_loaded', [9, current_page]);window['gf_submitting_9'] = false;}else if(!is_redirect){var confirmation_content = jQuery(this).contents().find('.GF_AJAX_POSTBACK').html();if(!confirmation_content){confirmation_content = contents;}setTimeout(function(){jQuery('#gform_wrapper_9').replaceWith(confirmation_content);jQuery(document).trigger('gform_confirmation_loaded', [9]);window['gf_submitting_9'] = false;wp.a11y.speak(jQuery('#gform_confirmation_message_9').text());}, 50);}else{jQuery('#gform_9').append(contents);if(window['gformRedirect']) {gformRedirect();}}jQuery(document).trigger('gform_post_render', [9, current_page]);} );} );
                                          </script>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </section>
                  </div>
               </div> -->
         </div>
      </section>
      <div class="elementor-element elementor-element-7939abfc e-con-boxed e-flex e-con" data-id="7939abfc" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}">
         <div class="e-con-inner">
            <div class="elementor-element elementor-element-49cea185 e-con-full e-flex e-con" data-id="49cea185" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
               <div class="elementor-element elementor-element-15761ad5 elementor-widget elementor-widget-heading" data-id="15761ad5" data-element_type="widget" data-widget_type="heading.default">
                  <div class="elementor-widget-container">
                     <h4 class="elementor-heading-title elementor-size-default">Gallery</h4>
                  </div>
               </div>
               <div class="elementor-element elementor-element-790240d elementor-grid-4 elementor-grid-tablet-2 elementor-grid-mobile-1 pp-ins-normal elementor-widget elementor-widget-pp-image-gallery" data-id="790240d" data-element_type="widget" data-settings="{&quot;columns&quot;:&quot;4&quot;,&quot;lightbox_library&quot;:&quot;fancybox&quot;,&quot;pagination&quot;:&quot;yes&quot;,&quot;columns_tablet&quot;:&quot;2&quot;,&quot;columns_mobile&quot;:&quot;1&quot;}" data-widget_type="pp-image-gallery.default">
                  <div class="elementor-widget-container">
                     <div class="pp-image-gallery-container" data-settings="{&quot;tilt_enable&quot;:&quot;no&quot;,&quot;layout&quot;:&quot;grid&quot;,&quot;pagination&quot;:&quot;yes&quot;,&quot;per_page&quot;:&quot;8&quot;,&quot;post_id&quot;:11921,&quot;template_id&quot;:12556,&quot;widget_id&quot;:&quot;790240d&quot;}">
                        <div class="pp-image-gallery-wrapper">
                           <div class="pp-image-gallery pp-elementor-grid" id="pp-image-gallery-790240d" data-fancybox-settings="{&quot;loop&quot;:true,&quot;arrows&quot;:true,&quot;infobar&quot;:true,&quot;keyboard&quot;:true,&quot;toolbar&quot;:true,&quot;buttons&quot;:[&quot;zoom&quot;,&quot;slideShow&quot;,&quot;thumbs&quot;,&quot;close&quot;],&quot;animationEffect&quot;:&quot;zoom&quot;,&quot;transitionEffect&quot;:&quot;fade&quot;,&quot;baseClass&quot;:&quot;pp-gallery-fancybox pp-gallery-fancybox-790240d  pp-fancybox-thumbs-x&quot;,&quot;thumbs&quot;:{&quot;autoStart&quot;:true,&quot;axis&quot;:&quot;x&quot;}}">
                              @foreach ($galleryImages as $image)
                              <div class="pp-grid-item-wrap pp-group-1" data-item-id="15110">
                                 <div class="pp-grid-item pp-image">
                                    <div class="pp-image-gallery-thumbnail-wrap pp-ins-filter-hover">
                                       <a data-elementor-open-lightbox="no" data-fancybox="pp-image-gallery-790240d" href="{{ $image }}" class="pp-image-gallery-item-link"></a>
                                       <div class="pp-ins-filter-target pp-image-gallery-thumbnail"><img class="pp-gallery-slide-image" src="{{ $image }}" alt="" data-no-lazy="1" /></div>
                                       <div class="pp-image-overlay pp-media-overlay"></div>
                                       <div class="pp-gallery-image-content pp-media-content"></div>
                                    </div>
                                 </div>
                              </div>
                              @endforeach
                              <!-- <div class="pp-grid-item-wrap pp-group-1" data-item-id="15111">
                                 <div class="pp-grid-item pp-image">
                                    <div class="pp-image-gallery-thumbnail-wrap pp-ins-filter-hover">
                                       <a data-elementor-open-lightbox="no" data-fancybox="pp-image-gallery-790240d" href="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-05.jpg" class="pp-image-gallery-item-link"></a>
                                       <div class="pp-ins-filter-target pp-image-gallery-thumbnail"><img class="pp-gallery-slide-image" src="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-05.jpg" alt="" data-no-lazy="1" /></div>
                                       <div class="pp-image-overlay pp-media-overlay"></div>
                                       <div class="pp-gallery-image-content pp-media-content"></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="pp-grid-item-wrap pp-group-1" data-item-id="15112">
                                 <div class="pp-grid-item pp-image">
                                    <div class="pp-image-gallery-thumbnail-wrap pp-ins-filter-hover">
                                       <a data-elementor-open-lightbox="no" data-fancybox="pp-image-gallery-790240d" href="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-06.jpg" class="pp-image-gallery-item-link"></a>
                                       <div class="pp-ins-filter-target pp-image-gallery-thumbnail"><img class="pp-gallery-slide-image" src="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-06.jpg" alt="" data-no-lazy="1" /></div>
                                       <div class="pp-image-overlay pp-media-overlay"></div>
                                       <div class="pp-gallery-image-content pp-media-content"></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="pp-grid-item-wrap pp-group-1" data-item-id="15113">
                                 <div class="pp-grid-item pp-image">
                                    <div class="pp-image-gallery-thumbnail-wrap pp-ins-filter-hover">
                                       <a data-elementor-open-lightbox="no" data-fancybox="pp-image-gallery-790240d" href="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-07.jpg" class="pp-image-gallery-item-link"></a>
                                       <div class="pp-ins-filter-target pp-image-gallery-thumbnail"><img class="pp-gallery-slide-image" src="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-07.jpg" alt="" data-no-lazy="1" /></div>
                                       <div class="pp-image-overlay pp-media-overlay"></div>
                                       <div class="pp-gallery-image-content pp-media-content"></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="pp-grid-item-wrap pp-group-1" data-item-id="15114">
                                 <div class="pp-grid-item pp-image">
                                    <div class="pp-image-gallery-thumbnail-wrap pp-ins-filter-hover">
                                       <a data-elementor-open-lightbox="no" data-fancybox="pp-image-gallery-790240d" href="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-08.jpg" class="pp-image-gallery-item-link"></a>
                                       <div class="pp-ins-filter-target pp-image-gallery-thumbnail"><img class="pp-gallery-slide-image" src="https://propertyinfo.gr/wp-content/uploads/2023/03/Karidi-Harmony-Apartment-A-Vourvourou-Sithonia-Halkidiki-Greece-08.jpg" alt="" data-no-lazy="1" /></div>
                                       <div class="pp-image-overlay pp-media-overlay"></div>
                                       <div class="pp-gallery-image-content pp-media-content"></div>
                                    </div>
                                 </div>
                              </div> -->
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="elementor-element elementor-element-100c2edf e-con-boxed e-flex e-con" data-id="100c2edf" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}">
         <div class="e-con-inner">
            <div class="elementor-element elementor-element-1ff1c6a9 e-con-full e-flex e-con" data-id="1ff1c6a9" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
               <div class="elementor-element elementor-element-3fa38696 elementor-widget elementor-widget-heading" data-id="3fa38696" data-element_type="widget" data-widget_type="heading.default">
                  <div class="elementor-widget-container">
                     <h4 class="elementor-heading-title elementor-size-default">Experience Our Vacation Rental through Video</h4>
                  </div>
               </div>
               <div class="elementor-element elementor-element-b53b32e elementor-widget elementor-widget-video" data-id="b53b32e" data-element_type="widget" data-settings="{&quot;youtube_url&quot;:&quot;https:\/\/youtu.be\/9Rg7NPiH6yw&quot;,&quot;lazy_load&quot;:&quot;yes&quot;,&quot;video_type&quot;:&quot;youtube&quot;,&quot;controls&quot;:&quot;yes&quot;}" data-widget_type="video.default">
                  <div class="elementor-widget-container">
                     <style>
                        /*! elementor - v3.13.0 - 08-05-2023 */
                        .elementor-widget-video .elementor-widget-container {
                           overflow: hidden;
                           transform: translateZ(0)
                        }

                        .elementor-widget-video .elementor-wrapper {
                           aspect-ratio: var(--video-aspect-ratio)
                        }

                        .elementor-widget-video .elementor-wrapper iframe,
                        .elementor-widget-video .elementor-wrapper video {
                           height: 100%;
                           width: 100%;
                           display: flex;
                           border: none;
                           background-color: #000
                        }

                        @supports not (aspect-ratio:1/1) {
                           .elementor-widget-video .elementor-wrapper {
                              position: relative;
                              overflow: hidden;
                              height: 0;
                              padding-bottom: calc(100% / var(--video-aspect-ratio))
                           }

                           .elementor-widget-video .elementor-wrapper iframe,
                           .elementor-widget-video .elementor-wrapper video {
                              position: absolute;
                              top: 0;
                              right: 0;
                              bottom: 0;
                              left: 0
                           }
                        }

                        .elementor-widget-video .elementor-open-inline .elementor-custom-embed-image-overlay {
                           position: absolute;
                           top: 0;
                           left: 0;
                           width: 100%;
                           height: 100%;
                           background-size: cover;
                           background-position: 50%
                        }

                        .elementor-widget-video .elementor-custom-embed-image-overlay {
                           cursor: pointer;
                           text-align: center
                        }

                        .elementor-widget-video .elementor-custom-embed-image-overlay:hover .elementor-custom-embed-play i {
                           opacity: 1
                        }

                        .elementor-widget-video .elementor-custom-embed-image-overlay img {
                           display: block;
                           width: 100%
                        }

                        .elementor-widget-video .e-hosted-video .elementor-video {
                           -o-object-fit: cover;
                           object-fit: cover
                        }

                        .e-con-inner>.elementor-widget-video,
                        .e-con>.elementor-widget-video {
                           width: var(--container-widget-width);
                           --flex-grow: var(--container-widget-flex-grow)
                        }
                     </style>
                     <div class="elementor-wrapper elementor-open-inline">
                        <div class="elementor-video"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <section class="elementor-section elementor-top-section elementor-element elementor-element-2fd4185 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="2fd4185" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
         <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-17ff3453" data-id="17ff3453" data-element_type="column">
               <div class="elementor-widget-wrap elementor-element-populated">
                  <div class="elementor-element elementor-element-2177dae4 elementor-widget elementor-widget-shortcode" data-id="2177dae4" data-element_type="widget" data-widget_type="shortcode.default">
                     <div class="elementor-widget-container">
                     </div>
                  </div>
                  <div class="elementor-shortcode"></div>
               </div>
            </div>
            <div class="elementor-element elementor-element-4c3cc204 elementor-grid-4 elementor-hidden-desktop elementor-hidden-tablet elementor-hidden-mobile elementor-grid-tablet-2 elementor-grid-mobile-1 elementor-widget elementor-widget-loop-grid" data-id="4c3cc204" data-element_type="widget" data-settings="{&quot;template_id&quot;:14281,&quot;columns&quot;:4,&quot;row_gap_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:0,&quot;sizes&quot;:[]},&quot;_skin&quot;:&quot;post&quot;,&quot;columns_tablet&quot;:&quot;2&quot;,&quot;columns_mobile&quot;:&quot;1&quot;,&quot;edit_handle_selector&quot;:&quot;[data-elementor-type=\&quot;loop-item\&quot;]&quot;,&quot;row_gap&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;row_gap_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}" data-widget_type="loop-grid.post">
               <div class="elementor-widget-container">
                  <link rel="stylesheet" href="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/css/widget-loop-builder.min.css">
                  <div class="elementor-loop-container elementor-grid">
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
   <script type="speculationrules">
      {"prefetch":[{"source":"document","where":{"and":[{"href_matches":"\/*"},{"not":{"href_matches":["\/wp-*.php","\/wp-admin\/*","\/wp-content\/uploads\/*","\/wp-content\/*","\/wp-content\/plugins\/*","\/wp-content\/themes\/generatepress_child\/*","\/wp-content\/themes\/generatepress\/*","\/*\\?(.+)"]}},{"not":{"selector_matches":"a[rel~=\"nofollow\"]"}},{"not":{"selector_matches":".no-prefetch, .no-prefetch a"}}]},"eagerness":"conservative"}]}
      </script>
   <script id="generate-a11y">
      ! function() {
         "use strict";
         if ("querySelector" in document && "addEventListener" in window) {
            var e = document.body;
            e.addEventListener("mousedown", function() {
               e.classList.add("using-mouse")
            }), e.addEventListener("keydown", function() {
               e.classList.remove("using-mouse")
            })
         }
      }();
   </script>
   <script type='text/javascript'>
      const lazyloadRunObserver = () => {
         const dataAttribute = 'data-e-bg-lazyload';
         const lazyloadBackgrounds = document.querySelectorAll(`[${ dataAttribute }]:not(.lazyloaded)`);
         const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
               if (entry.isIntersecting) {
                  let lazyloadBackground = entry.target;
                  const lazyloadSelector = lazyloadBackground.getAttribute(dataAttribute);
                  if (lazyloadSelector) {
                     lazyloadBackground = entry.target.querySelector(lazyloadSelector);
                  }
                  if (lazyloadBackground) {
                     lazyloadBackground.classList.add('lazyloaded');
                  }
                  lazyloadBackgroundObserver.unobserve(entry.target);
               }
            });
         }, {
            rootMargin: '100px 0px 100px 0px'
         });
         lazyloadBackgrounds.forEach((lazyloadBackground) => {
            lazyloadBackgroundObserver.observe(lazyloadBackground);
         });
      };
      const events = [
         'DOMContentLoaded',
         'elementor/lazyload/observe',
      ];
      events.forEach((event) => {
         document.addEventListener(event, lazyloadRunObserver);
      });
   </script>
   <link rel='stylesheet' id='gravity_forms_theme_reset-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-reset.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gravity_forms_theme_foundation-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-foundation.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gravity_forms_theme_framework-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-theme-framework.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gravity_forms_orbital_theme-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/gravity-forms-orbital-theme.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='fancybox-css' href='https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/lib/fancybox/jquery.fancybox.min.css?ver=2.9.16' media='all' />
   <link rel='stylesheet' id='gform_basic-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/basic.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gform_theme_components-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/theme-components.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gform_theme_ie11-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/theme-ie11.min.css?ver=2.7.5' media='all' />
   <link rel='stylesheet' id='gform_theme-css' href='https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/css/dist/theme.min.css?ver=2.7.5' media='all' />
   <!--[if lte IE 11]>
      <script src="https://propertyinfo.gr/wp-content/themes/generatepress/assets/js/classList.min.js?ver=3.3.0" id="generate-classlist-js"></script>
      <![endif]-->
   <script id="generate-menu-js-extra">
      var generatepressMenu = {
         "toggleOpenedSubMenus": "1",
         "openSubMenuLabel": "Open Sub-Menu",
         "closeSubMenuLabel": "Close Sub-Menu"
      };
   </script>
   <script src="https://propertyinfo.gr/wp-content/themes/generatepress/assets/js/menu.min.js?ver=3.3.0" id="generate-menu-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/comment-reply.min.js?ver=6.8.1" id="comment-reply-js" async data-wp-strategy="async"></script>
   <script id="jquery-core-js-extra">
      var pp = {
         "ajax_url": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php"
      };
      var pp = {
         "ajax_url": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php"
      };
   </script>
   <script src="https://propertyinfo.gr/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
   <script id="powerpack-frontend-js-extra">
      var ppLogin = {
         "empty_username": "Enter a username or email address.",
         "empty_password": "Enter password.",
         "empty_password_1": "Enter a password.",
         "empty_password_2": "Re-enter password.",
         "empty_recaptcha": "Please check the captcha to verify you are not a robot.",
         "email_sent": "A password reset email has been sent to the email address for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.",
         "reset_success": "Your password has been reset successfully.",
         "ajax_url": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php",
         "show_password": "Show password",
         "hide_password": "Hide password"
      };
      var ppRegistration = {
         "invalid_username": "This username is invalid because it uses illegal characters. Please enter a valid username.",
         "username_exists": "This username is already registered. Please choose another one.",
         "empty_email": "Please type your email address.",
         "invalid_email": "The email address isn\u2019t correct!",
         "email_exists": "The email is already registered, please choose another one.",
         "password": "Password must not contain the character \"\\\\\"",
         "password_length": "Your password should be at least 8 characters long.",
         "password_mismatch": "Password does not match.",
         "invalid_url": "URL seems to be invalid.",
         "recaptcha_php_ver": "reCAPTCHA API requires PHP version 5.3 or above.",
         "recaptcha_missing_key": "Your reCAPTCHA Site or Secret Key is missing!",
         "show_password": "Show password",
         "hide_password": "Hide password",
         "ajax_url": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php"
      };
      var ppCoupons = {
         "copied_text": "Copied"
      };
   </script>
   <script src="https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/js/min/frontend.min.js?ver=2.9.16" id="powerpack-frontend-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/lib/fancybox/jquery.fancybox.min.js?ver=2.9.16" id="jquery-fancybox-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/imagesloaded.min.js?ver=5.0.0" id="imagesloaded-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/dist/dom-ready.min.js?ver=f77871ff7694fffea381" id="wp-dom-ready-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/dist/hooks.min.js?ver=4d63a3d491d11ffd8ac6" id="wp-hooks-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/dist/i18n.min.js?ver=5e580eb46a90c2b997e6" id="wp-i18n-js"></script>
   <script id="wp-i18n-js-after">
      wp.i18n.setLocaleData({
         'text direction\u0004ltr': ['ltr']
      });
   </script>
   <script src="https://propertyinfo.gr/wp-includes/js/dist/a11y.min.js?ver=3156534cc54473497e14" id="wp-a11y-js"></script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/js/jquery.json.min.js?ver=2.7.5" id="gform_json-js"></script>
   <script id="gform_gravityforms-js-extra">
      var gform_i18n = {
         "datepicker": {
            "days": {
               "monday": "Mo",
               "tuesday": "Tu",
               "wednesday": "We",
               "thursday": "Th",
               "friday": "Fr",
               "saturday": "Sa",
               "sunday": "Su"
            },
            "months": {
               "january": "January",
               "february": "February",
               "march": "March",
               "april": "April",
               "may": "May",
               "june": "June",
               "july": "July",
               "august": "August",
               "september": "September",
               "october": "October",
               "november": "November",
               "december": "December"
            },
            "firstDay": 1,
            "iconText": "Select date"
         }
      };
      var gf_legacy_multi = [];
      var gform_gravityforms = {
         "strings": {
            "invalid_file_extension": "This type of file is not allowed. Must be one of the following:",
            "delete_file": "Delete this file",
            "in_progress": "in progress",
            "file_exceeds_limit": "File exceeds size limit",
            "illegal_extension": "This type of file is not allowed.",
            "max_reached": "Maximum number of files reached",
            "unknown_error": "There was a problem while saving the file on the server",
            "currently_uploading": "Please wait for the uploading to complete",
            "cancel": "Cancel",
            "cancel_upload": "Cancel this upload",
            "cancelled": "Cancelled"
         },
         "vars": {
            "images_url": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityforms\/images"
         }
      };
      var gf_global = {
         "gf_currency_config": {
            "name": "Euro",
            "symbol_left": "",
            "symbol_right": "&#8364;",
            "symbol_padding": " ",
            "thousand_separator": ".",
            "decimal_separator": ",",
            "decimals": 2,
            "code": "EUR"
         },
         "base_url": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityforms",
         "number_formats": [],
         "spinnerUrl": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityforms\/images\/spinner.svg",
         "version_hash": "f86d837681648e471c95adb83c5ccee0",
         "strings": {
            "newRowAdded": "New row added.",
            "rowRemoved": "Row removed",
            "formSaved": "The form has been saved.  The content contains the link to return and complete the form."
         }
      };
   </script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/js/gravityforms.min.js?ver=2.7.5" id="gform_gravityforms-js"></script>
   <script id="gform_conditional_logic-js-extra">
      var gf_legacy = {
         "is_legacy": ""
      };
   </script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/js/conditional_logic.min.js?ver=2.7.5" id="gform_conditional_logic-js"></script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/js/dist/utils.min.js?ver=3f33f1b56bb5e5da665be32ebbe89543" id="gform_gravityforms_utils-js"></script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/js/dist/vendor-theme.min.js?ver=1a5749916bf8ca4822137a83fec75744" id="gform_gravityforms_theme_vendors-js"></script>
   <script id="gform_gravityforms_theme-js-extra">
      var gform_theme_config = {
         "common": {
            "form": {
               "honeypot": {
                  "version_hash": "f86d837681648e471c95adb83c5ccee0"
               }
            }
         },
         "hmr_dev": "",
         "public_path": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/gravityforms\/assets\/js\/dist\/"
      };
   </script>
   <script defer='defer' src="https://propertyinfo.gr/wp-content/plugins/gravityforms/assets/js/dist/scripts-theme.min.js?ver=c0c5d795571af887e62fb8d599d4d553" id="gform_gravityforms_theme-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/lib/isotope/isotope.pkgd.min.js?ver=0.5.3" id="isotope-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/js/webpack-pro.runtime.min.js?ver=3.13.0" id="elementor-pro-webpack-runtime-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.13.0" id="elementor-webpack-runtime-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.13.0" id="elementor-frontend-modules-js"></script>
   <script id="elementor-pro-frontend-js-before">
      var ElementorProFrontendConfig = {
         "ajaxurl": "https:\/\/propertyinfo.gr\/wp-admin\/admin-ajax.php",
         "nonce": "ff7a8a77a9",
         "urls": {
            "assets": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/elementor-pro\/assets\/",
            "rest": "https:\/\/propertyinfo.gr\/wp-json\/"
         },
         "shareButtonsNetworks": {
            "facebook": {
               "title": "Facebook",
               "has_counter": true
            },
            "twitter": {
               "title": "Twitter"
            },
            "linkedin": {
               "title": "LinkedIn",
               "has_counter": true
            },
            "pinterest": {
               "title": "Pinterest",
               "has_counter": true
            },
            "reddit": {
               "title": "Reddit",
               "has_counter": true
            },
            "vk": {
               "title": "VK",
               "has_counter": true
            },
            "odnoklassniki": {
               "title": "OK",
               "has_counter": true
            },
            "tumblr": {
               "title": "Tumblr"
            },
            "digg": {
               "title": "Digg"
            },
            "skype": {
               "title": "Skype"
            },
            "stumbleupon": {
               "title": "StumbleUpon",
               "has_counter": true
            },
            "mix": {
               "title": "Mix"
            },
            "telegram": {
               "title": "Telegram"
            },
            "pocket": {
               "title": "Pocket",
               "has_counter": true
            },
            "xing": {
               "title": "XING",
               "has_counter": true
            },
            "whatsapp": {
               "title": "WhatsApp"
            },
            "email": {
               "title": "Email"
            },
            "print": {
               "title": "Print"
            }
         },
         "facebook_sdk": {
            "lang": "en_US",
            "app_id": ""
         },
         "lottie": {
            "defaultAnimationUrl": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/elementor-pro\/modules\/lottie\/assets\/animations\/default.json"
         }
      };
   </script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/js/frontend.min.js?ver=3.13.0" id="elementor-pro-frontend-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor/assets/lib/waypoints/waypoints.min.js?ver=4.0.2" id="elementor-waypoints-js"></script>
   <script src="https://propertyinfo.gr/wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
   <script id="elementor-frontend-js-before">
      var elementorFrontendConfig = {
         "environmentMode": {
            "edit": false,
            "wpPreview": false,
            "isScriptDebug": false
         },
         "i18n": {
            "shareOnFacebook": "Share on Facebook",
            "shareOnTwitter": "Share on Twitter",
            "pinIt": "Pin it",
            "download": "Download",
            "downloadImage": "Download image",
            "fullscreen": "Fullscreen",
            "zoom": "Zoom",
            "share": "Share",
            "playVideo": "Play Video",
            "previous": "Previous",
            "next": "Next",
            "close": "Close"
         },
         "is_rtl": false,
         "breakpoints": {
            "xs": 0,
            "sm": 480,
            "md": 768,
            "lg": 1025,
            "xl": 1440,
            "xxl": 1600
         },
         "responsive": {
            "breakpoints": {
               "mobile": {
                  "label": "Mobile Portrait",
                  "value": 767,
                  "default_value": 767,
                  "direction": "max",
                  "is_enabled": true
               },
               "mobile_extra": {
                  "label": "Mobile Landscape",
                  "value": 880,
                  "default_value": 880,
                  "direction": "max",
                  "is_enabled": false
               },
               "tablet": {
                  "label": "Tablet Portrait",
                  "value": 1024,
                  "default_value": 1024,
                  "direction": "max",
                  "is_enabled": true
               },
               "tablet_extra": {
                  "label": "Tablet Landscape",
                  "value": 1200,
                  "default_value": 1200,
                  "direction": "max",
                  "is_enabled": false
               },
               "laptop": {
                  "label": "Laptop",
                  "value": 1366,
                  "default_value": 1366,
                  "direction": "max",
                  "is_enabled": false
               },
               "widescreen": {
                  "label": "Widescreen",
                  "value": 2400,
                  "default_value": 2400,
                  "direction": "min",
                  "is_enabled": false
               }
            }
         },
         "version": "3.13.0",
         "is_static": false,
         "experimentalFeatures": {
            "e_dom_optimization": true,
            "e_optimized_assets_loading": true,
            "e_optimized_css_loading": true,
            "e_font_icon_svg": true,
            "a11y_improvements": true,
            "additional_custom_breakpoints": true,
            "container": true,
            "theme_builder_v2": true,
            "landing-pages": true,
            "nested-elements": true,
            "e_lazyload": true,
            "page-transitions": true,
            "notes": true,
            "loop": true,
            "form-submissions": true,
            "e_scroll_snap": true
         },
         "urls": {
            "assets": "https:\/\/propertyinfo.gr\/wp-content\/plugins\/elementor\/assets\/"
         },
         "swiperClass": "swiper-container",
         "settings": {
            "page": [],
            "editorPreferences": []
         },
         "kit": {
            "active_breakpoints": ["viewport_mobile", "viewport_tablet"],
            "global_image_lightbox": "yes",
            "lightbox_enable_counter": "yes",
            "lightbox_enable_fullscreen": "yes",
            "lightbox_enable_zoom": "yes",
            "lightbox_enable_share": "yes",
            "lightbox_title_src": "title",
            "lightbox_description_src": "description"
         },
         "post": {
            "id": 11921,
            "title": "Villa%20Kalipso%20%E2%80%93%20Property%20Info",
            "excerpt": "",
            "featuredImage": false
         }
      };
   </script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.13.0" id="elementor-frontend-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/js/elements-handlers.min.js?ver=3.13.0" id="pro-elements-handlers-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/elementor-pro/assets/lib/sticky/jquery.sticky.min.js?ver=3.13.0" id="e-sticky-js"></script>
   <script src="https://propertyinfo.gr/wp-content/plugins/powerpack-elements/assets/lib/tooltipster/tooltipster.min.js?ver=2.9.16" id="pp-tooltipster-js"></script>
   <script>
      gform.initializeOnLoaded(function() {
         jQuery(document).on('gform_post_render', function(event, formId, currentPage) {
            if (formId == 9) {
               gf_global["number_formats"][9] = {
                  "7": {
                     "price": false,
                     "value": false
                  },
                  "1": {
                     "price": false,
                     "value": false
                  },
                  "4": {
                     "price": false,
                     "value": false
                  },
                  "10": {
                     "price": false,
                     "value": false
                  },
                  "11": {
                     "price": false,
                     "value": false
                  },
                  "2": {
                     "price": false,
                     "value": false
                  },
                  "5": {
                     "price": false,
                     "value": false
                  },
                  "12": {
                     "price": false,
                     "value": false
                  },
                  "8": {
                     "price": false,
                     "value": false
                  },
                  "3": {
                     "price": false,
                     "value": false
                  }
               };
               if (window['jQuery']) {
                  if (!window['gf_form_conditional_logic']) window['gf_form_conditional_logic'] = new Array();
                  window['gf_form_conditional_logic'][9] = {
                     logic: {
                        5: {
                           "field": {
                              "actionType": "show",
                              "logicType": "all",
                              "rules": [{
                                 "fieldId": "11",
                                 "operator": "is",
                                 "value": "Phone"
                              }],
                              "enabled": true
                           },
                           "nextButton": null,
                           "section": ""
                        },
                        12: {
                           "field": {
                              "actionType": "show",
                              "logicType": "all",
                              "rules": [{
                                 "fieldId": "11",
                                 "operator": "is",
                                 "value": "Phone"
                              }],
                              "enabled": true
                           },
                           "nextButton": null,
                           "section": ""
                        }
                     },
                     dependents: {
                        5: [5],
                        12: [12]
                     },
                     animation: 0,
                     defaults: {
                        "1": {
                           "1.2": "",
                           "1.3": "",
                           "1.4": "",
                           "1.6": "",
                           "1.8": ""
                        },
                        "4": {
                           "4.1": "",
                           "4.2": "",
                           "4.3": "",
                           "4.4": "Virginia",
                           "4.5": "",
                           "4.6": "United States"
                        },
                        "2": {
                           "2": "",
                           "2.2": ""
                        },
                        "12": ""
                     },
                     fields: {
                        "7": [],
                        "1": [],
                        "4": [],
                        "10": [],
                        "11": [5, 12],
                        "2": [],
                        "5": [],
                        "12": [],
                        "8": [],
                        "3": []
                     }
                  };
                  if (!window['gf_number_format']) window['gf_number_format'] = 'decimal_dot';
                  jQuery(document).ready(function() {
                     window['gformInitPriceFields']();
                     gf_apply_rules(9, [5, 12], true);
                     jQuery('#gform_wrapper_9').show();
                     jQuery(document).trigger('gform_post_conditional_logic', [9, null, true]);
                  });
               }
            }
         });
         jQuery(document).bind('gform_post_conditional_logic', function(event, formId, fields, isInit) {})
      });
   </script>
   <script>
      gform.initializeOnLoaded(function() {
         jQuery(document).trigger('gform_post_render', [9, 1])
      });
   </script>

   

   <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap">
   </script>
</body>

</html>