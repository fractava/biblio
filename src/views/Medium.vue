<template>
	<div>
		<MediumUI :title="medium.title"
			:fields="medium.fields"
			@setTitle="setTitle"
			@setFields="setFields" />
  </div>
</template>

<!--<script>

const medium = ref();

</script>-->

<script>
import Draggable from "vuedraggable";

import MediumUI from "./MediumUI.vue";

import { useMediumsStore } from "../store/mediums.js";
import { mapStores } from "pinia";

export default {
	components: {
		Draggable,
		MediumUI,
	},
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			newMedium: {
				title: "Test",
				fields: []
			}
		};
	},
	computed: {
		...mapStores(useMediumsStore),
		mediumId() {
			return this.$route.params.id;
		},
		fetchedMedium() {
			if (this.createNew) {
				return false;
			} else {
				return this.mediumsStore.getMediumById(this.mediumId);
			}
		},
		medium() {
			if (this.createNew) {
				return this.newMedium;
			} else {
				return this.fetchedMedium;
			}
		}
	},
	watch: {
	},
	mounted() {
	},
	methods: {
		setTitle(newTitle) {
			if (this.createNew) {
				this.newMedium.title = newTitle;
			} else {
				// TODO
			}
		},
		setFields(newFields) {
			if (this.createNew) {
				this.newMedium.fields = newFields;
			} else {
				// TODO
			}
		},
	},
};
</script>
