<template>
	<div>
		<p>{{ $route.params.id }}</p>
		<p>{{ createNew }}</p>
		<p>{{ thisTitle }}</p>
		<input v-model="thisTitle">
	</div>
</template>

<script>

import { mapState } from 'vuex'

export default {
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			newTitle: '',
			newData: '',
		}
	},
	computed: {
		...mapState({
			mediums: state => state.mediums.mediums,
		}),
		thisTitle: {
			get() {
				if (this.createNew) {
                    return this.newTitle;
                } else {
                    return this.$store.getters.getMediumById(this.$route.params.id).title;
                }
			},
			set(value) {
                if(this.createNew) {
                    this.newTitle = value;
                } else {
				    this.$store.dispatch('updateMediumTitle', {id: this.$route.params.id, title: value});
                }
			},
		},
	},
}
</script>
