import Vue from "vue";
import Vuex from "vuex";

import mediums from "./mediums.js";

Vue.use(Vuex);

const store = new Vuex.Store({
	modules: {
		mediums,
	},
});

export default store;
