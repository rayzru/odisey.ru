tinymce.init({
	selector: 'textarea.tinymce',
	theme: "modern",
	menubar: false,
	language: 'ru',
	language_url: '/assets/js/tinymce.ru.js',
	plugins: [
		"advlist autolink link image lists preview hr anchor",
		"visualblocks visualchars media",
		"table directionality paste textcolor responsivefilemanager code"
	],
	toolbar1: "bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | styleselect | responsivefilemanager | link unlink anchor | image media | print preview code ",
	toolbar2: "",
	relative_urls: false,
	image_advtab: true,
	external_filemanager_path: "/vendor/trippo/ResponsiveFilemanager/filemanager/",
	filemanager_title: "Загрузка",
	external_plugins: {"filemanager": "/vendor/trippo/ResponsiveFilemanager/filemanager/plugin.min.js"}
});