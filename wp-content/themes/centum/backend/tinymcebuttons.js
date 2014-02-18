// JavaScript Document
(function() {
    tinymce.create('tinymce.plugins.purethemesmce', {
        init : function(ed, url) {

            ed.addCommand('ppTabs', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/tabs.php',
                    width : 500,
                    height : 500,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('pptabs', {title : 'Add Tabs', cmd : 'ppTabs', image: url + '/tinymce/images/tab.png' });

            ed.addCommand('ppSlideshow', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/slideshow.php',
                    width : 500,
                    height : 500,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppslideshow', {title : 'Add Slideshow', cmd : 'ppSlideshow', image: url + '/tinymce/images/showreel.png' });

            ed.addCommand('ppAccordion', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/accordion.php',
                    width : 500,
                    height : 500,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppaccordion', {title : 'Add Accordion', cmd : 'ppAccordion', image: url + '/tinymce/images/accordion.png' });


            ed.addCommand('ppToggle', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/toggle.php',
                    width : 500,
                    height : 500,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('pptoggle', {title : 'Add Toggle', cmd : 'ppToggle', image: url + '/tinymce/images/toggle.png' });


            ed.addCommand('ppColumns', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/columns.php',
                    width : 450,
                    height : 500,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppcolumns', {title : 'Add Columns', cmd : 'ppColumns', image: url + '/tinymce/images/columns.png' });

            ed.addCommand('ppBoxes', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/boxes.php',
                    width : 450,
                    height : 370,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppboxes', {title : 'Add Box', cmd : 'ppBoxes', image: url + '/tinymce/images/box.png' });

            ed.addCommand('ppSocialicon', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/social.php',
                    width : 450,
                    height : 370,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppsocial', {title : 'Add Social button', cmd : 'ppSocialicon', image: url + '/tinymce/images/twitter.png' });

            ed.addCommand('ppButton', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/button.php',
                    width : 450,
                    height : 470,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('ppbutton', {title : 'Add Button', cmd : 'ppButton', image: url + '/tinymce/images/brick.png' });

            ed.addButton('headline', {
                title : 'Headline',
                image : url+'/tinymce/images/premium.png',
                onclick : function() {
                     ed.selection.setContent('[headline]' + ed.selection.getContent() + '[/headline]');

                }
            });

            ed.addButton('hr', {
                title : 'Separator',
                image : url+'/tinymce/images/link_go.png',
                onclick : function() {
                     ed.selection.setContent('[separator]');

                }
            });


            ed.addButton('slider', {
                title : 'Add Slider',
                image : url+'/tinymce/images/showreel.png',
                onclick : function() {
                     ed.selection.setContent('[slider title="Put Title"]' + ed.selection.getContent() + '[/slider]');

                }
            });

         ed.addCommand('pplist', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/list.php',
                    width : 450,
                    height : 370,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
         ed.addButton('list', { title : 'Add List', image : url+'/tinymce/images/list.png', cmd: 'pplist' });

         ed.addCommand('bignotice', function() {
                ed.windowManager.open({
                    file : url + '/tinymce/notice.php',
                    width : 450,
                    height : 370,
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
         ed.addButton('ppnotice', { title : 'Add Notice', image : url+'/tinymce/images/notice.png', cmd: 'bignotice' });

         ed.addCommand('feature', function() {
            ed.windowManager.open({
                file : url + '/tinymce/feature.php',
                width : 450,
                height : 370,
                inline : 1
            }, {
                plugin_url : url
            });
        });
         ed.addButton('feature', { title : 'Add Feature', image : url+'/tinymce/images/feature.png', cmd: 'feature' });

        },
        createControl : function(n, cm) {
              switch (n) {
            case 'columns':
                var mlb = cm.createListBox('columns', {
                    title : 'Quick shortcodes',
                    onselect : function(v) {
                        tinyMCE.activeEditor.selection.setContent(v);

                    }
                });

                // Add some values to the list box
                mlb.add('1/3 + 2/3', '[column width="1/3" place="first" ] [/column] [column width="2/3" place="last" ] [/column] ');
                mlb.add('1/3 + 1/3 + 1/3 ', '[column width="1/3" place="first" ] [/column] [column width="1/3" place="none" ] [/column] [column width="1/3" place="last" ] [/column] ');
                mlb.add('1/2 + 1/2', '[column width="eight" place="first" ] [/column] [column width="eight" place="last" ] [/column] ');
                mlb.add('Headline h4', '[headline htype="h4"] [/headline]');
                mlb.add('Headline low margin', '[headline margin="low-margin"] [/headline]');
                mlb.add('Headline margin', '[headline margin="margin"] [/headline]');

            // Return the new listbox instance
            return mlb;

        }
        return null;
        },

        getInfo : function(){
            return {
                longname: 'Purethemes TinyMCE Buttons',
                author: 'Purethemes',
                authorurl: 'http://purethemes.net/',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('purethemesmce', tinymce.plugins.purethemesmce);




})();