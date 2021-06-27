<template>
	<ul>
        <router-link to="medium/new">
            <a class="button">
                <span class="icon icon-add"></span>
                <span>{{ t('biblio', 'New Medium') }}</span>
            </a>
        </router-link>
		<Table
			:columns="['title', 'fields']"
			:items="mediums"
			@click="openMedium($event)" />
	</ul>
</template>

<script>

import Table from '../components/Table.vue'

import { mapState } from 'vuex'

export default {
	components: {
		Table,
	},
	computed: {
		...mapState({
			mediums: state => state.mediums.mediums,
		}),
	},
	methods: {
		openMedium(medium) {
			console.log(medium)
			this.$router.push({
				path: '/medium/' + medium,
			})
		},
	},
}

</script>
