<template>
	<ul>
		<router-link to="medium/new">
			<a class="button">
				<span class="icon icon-add" />
				<span>{{ t('biblio', 'New Medium') }}</span>
			</a>
		</router-link>
		<Table :columns="['title', 'fields']"
			:items="mediumsStore.mediums"
			@click="openMedium($event)" />
	</ul>
</template>

<script>
import { mapStores } from "pinia";

import Table from "../components/Table.vue";
import { useMediumsStore } from "../store/mediums.js";

export default {
	components: {
		Table,
	},
	computed: {
		...mapStores(useMediumsStore)
	},
	methods: {
		openMedium(medium) {
			this.$router.push({
				path: "/medium/" + medium,
			});
		},
	},
};

</script>
