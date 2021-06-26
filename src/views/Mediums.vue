<template>
	<ul>
		<Table
			:columns="['title', 'data']"
			:items="mediums" />
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

	},
}

</script>
