<template>
	<ul>
		<router-link to="item/new">
			<a class="button">
				<span class="icon icon-add" />
				<span>{{ t('biblio', 'New Item') }}</span>
			</a>
		</router-link>
		<Table :columns="['title', 'fields']"
			:items="biblioStore.items"
			@click="openItem($event)" />
	</ul>
</template>

<script>
import { mapStores } from "pinia";

import Table from "../components/Table.vue";
import { useBiblioStore } from "../store/biblio.js";

export default {
	components: {
		Table,
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	methods: {
		openItem(item) {
			this.$router.push({
				path: "/item/" + item,
			});
		},
	},
};

</script>
