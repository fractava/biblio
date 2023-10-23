
/**
 * @copyright Copyright (c) 2021 Jonathan Treffler
 * @author Jonathan Treffler <mail@jonathan-treffler.de>
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
import Router from "vue-router";
import { getRootUrl, generateUrl } from "@nextcloud/router";

import Home from "./views/Home.vue";
import LendReturn from "./views/LendReturn.vue";
import Items from "./views/Items.vue";
import Item from "./views/Item.vue";
import Customers from "./views/Customers.vue";
import Customer from "./views/Customer.vue";
import ItemInstances from "./views/ItemInstances.vue";

Vue.use(Router);

const webRootWithIndexPHP = getRootUrl() + "/index.php";
const doesURLContainIndexPHP = window.location.pathname.startsWith(webRootWithIndexPHP);
const base = generateUrl("apps/biblio", {}, {
	noRewrite: doesURLContainIndexPHP,
});

const router = new Router({
	// mode: 'history',
	base,
	routes: [
		{
			path: "/",
			name: "home",
			component: Home,
		},
		{
			path: "/:collectionId/lend-return",
			name: "lend-return",
			component: LendReturn,
		},
		{
			path: "/:collectionId/items",
			name: "items",
			component: Items,
		},
		{
			path: "/:collectionId/item/:id",
			name: "item",
			component: Item,
		},
		{
			path: "/:collectionId/iteminstances",
			name: "iteminstances",
			component: ItemInstances,
		},
		{
			path: "/:collectionId/customers",
			name: "customers",
			component: Customers,
		},
		{
			path: "/:collectionId/customer/:id",
			name: "customer",
			component: Customer,
		},
	],
});

export default router;
