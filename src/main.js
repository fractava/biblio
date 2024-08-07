/**
 * @copyright Copyright (c) 2018 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
import Vue from "vue";
import { createPinia, PiniaVuePlugin } from "pinia";
import { generateFilePath } from "@nextcloud/router";
import VuePapaParse from 'vue-papa-parse'

import App from "./App.vue";

import router from "./router.js";

import Tooltip from "@nextcloud/vue/dist/Directives/Tooltip.js";
import Focus from "@nextcloud/vue/dist/Directives/Focus.js";

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath("biblio", '', 'js/');

Vue.use(PiniaVuePlugin);
Vue.use(VuePapaParse);

Vue.directive("tooltip", Tooltip);
Vue.directive('focus', Focus);

Vue.mixin({ methods: { t, n } });

const pinia = createPinia();

window.biblioRouter = router;

export default new Vue({
	router,
	el: "#content",
	render: h => h(App),
	pinia,
});
