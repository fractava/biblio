import { defineStore } from "pinia";

export const useSettingsStore = defineStore("settings", {
	state: () => ({
		site: "home",
		context: {},
	}),
	actions: {
		setSite(site, context) {
			this.site = site;
			this.context = context || this.context || {};
		},
	},
});