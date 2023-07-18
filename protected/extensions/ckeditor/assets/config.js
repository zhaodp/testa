/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    //toolbar_Full =
//    ['Source','-','Save','NewPage','Preview','-','Templates'],
//        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
//        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
//        ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
//        '/',
//        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
//        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
//        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
//        ['Link','Unlink','Anchor'],
//        ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
//        '/',
//        ['Styles','Format','Font','FontSize'],
//        ['TextColor','BGColor']

    config.toolbar_Public =
        [
            ['Source','-','Bold','Italic','Underline','-','Font','FontSize','-','TextColor','BGColor'],
//            ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
            ['SelectAll','RemoveFormat'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],

            ['NumberedList','BulletedList','-','Outdent','Indent'],
            ['Image','-','Undo','Redo']
        ];

    config.toolbar_Knowledge =
        [
            ['Source'],
            ['Undo','Redo','-','SelectAll','RemoveFormat'],
            ['Bold','Italic','Underline','Strike'],
            ['Styles','Format','Font','FontSize'],
            ['TextColor','BGColor','-','Link','Unlink','Image']
        ];


};
