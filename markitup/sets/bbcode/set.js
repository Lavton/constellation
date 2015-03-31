// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	"/markitup/sets/bbcode/parser.php", // path to your BBCode parser
	markupSet: [
		{name:'Полужирный', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Курсив', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Подчёркивание', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'Картинка', key:'P', replaceWith:'[img][![Url]!][/img]'},
		{name:'Ссылка', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		{name:'Размер', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Большой', openWith:'[size=200]', closeWith:'[/size]' },
			{name:'Нормальный', openWith:'[size=100]', closeWith:'[/size]' },
			{name:'Маленький', openWith:'[size=50]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		{name:'Ненумерованный список', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Нумерованный список', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'Пункт списка', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Цитата', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Вставить код', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Удалить теги', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Предпросмотр', className:"preview", call:'preview' }
	]
}