<template>
	<div>
		<ItemUI :title="item.title"
			:fields="biblioStore.itemFields"
			:field-values="item.fieldValues"
			@setTitle="setTitle"
			@setFields="setFields" />
  </div>
</template>

<!--<script>

const item = ref();

</script>-->

<script>
import Draggable from "vuedraggable";

import ItemUI from "./ItemUI.vue";

import { useBiblioStore } from "../store/biblio.js";
import { mapStores } from "pinia";

export default {
	components: {
		Draggable,
		ItemUI,
	},
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			newItem: {
				title: "Test",
			},
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		itemId() {
			return this.$route.params.id;
		},
		fetchedItem() {
			if (this.createNew) {
				return false;
			} else {
				return this.biblioStore.getItemById(this.itemId);
			}
		},
		item() {
			if (this.createNew) {
				return this.newItem;
			} else {
				return this.fetchedItem;
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
				this.newItem.title = newTitle;
			} else {
				// TODO
			}
		},
		setFields(newFields) {
			if (this.createNew) {
				this.newItem.fields = newFields;
			} else {
				// TODO
			}
		},
	},
};
</script>
