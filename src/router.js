
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

import { createRouter, createWebHistory } from 'vue-router'
import { getRootUrl, generateUrl } from "@nextcloud/router";

import LendReturn from "./views/LendReturn.vue";
import Mediums from "./views/Mediums.vue";
import Medium from "./views/Medium.vue";
import Customers from "./views/Customers.vue";
import Customer from "./views/Customer.vue";

const webRootWithIndexPHP = getRootUrl() + "/index.php";
const doesURLContainIndexPHP = window.location.pathname.startsWith(webRootWithIndexPHP);
const base = generateUrl("apps/biblio", {}, {
	noRewrite: doesURLContainIndexPHP,
});

const routes = [
	{
		path: "/",
		redirect: "/lend-return",
	},
	{
		path: "/lend-return",
		component: LendReturn,
	},
	{
		path: "/mediums",
		component: Mediums,
	},
	{
		path: "/medium/new",
		component: Medium,
		props: { createNew: true },
	},
	{
		path: "/medium/:id",
		component: Medium,
	},
	{
		path: "/customers",
		component: Customers,
	},
	{
		path: "/customer/:id",
		component: Customer,
	},
];

const router = createRouter({
	// mode: 'history',
	base,
	routes,
});

export default router;
