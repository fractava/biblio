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
    methods: {

    },
	async mounted() {
		try {
			//const response = await axios.get(generateUrl('/apps/biblio/mediums'))
			//this.mediums = response.data
		} catch (e) {
			console.error(e)
			showError(t('biblio', 'Could not fetch mediums'))
		}
		this.loading = false
	},
    computed: {
        ...mapState({
            mediums: state => state.mediums.mediums,
        }),
    },
}

</script>