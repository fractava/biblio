import { defineStore } from "pinia";

import { useBiblioStore } from "./biblio.js";

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
	getters: {
		selectedCollection: (state) => {
			const biblioStore = useBiblioStore();

			return biblioStore.collections.find(collection => collection.id === state.context?.collectionId);
		},
	},
});
