export default {
	mode: "universal",
	target: "server",
	head: {
		title: process.env.npm_package_name || "",
		meta: [
			{ charset: "utf-8" },
			{ name: "viewport", content: "width=device-width, initial-scale=1" },
			{ hid: "description", name: "description", content: process.env.npm_package_description || "" }
		],
		link: [
			{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }
		]
	},
	css: [
	],
	plugins: [
		"@/plugins/vue-material.js",
	],
	components: true,
	buildModules: [
		"@nuxtjs/eslint-module"
	],
	modules: [
		"@nuxtjs/pwa"
	],
	build: {
	},
	server: {
		port: 3001,
		host: "0.0.0.0",
	}
};
