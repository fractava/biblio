<template>
	<div>
		<ItemUI :title="item.title"
			:fields="item.fields"
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

import { useItemsStore } from "../store/items.js";
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
				fields: [
					{
						type: "short",
						title: "Test Field",
						value: "Test Value",
					},
					{
						type: "short",
						title: "Test Field 2",
						value: "Test Value 2",
					},
				],
			},
		};
	},
	computed: {
		...mapStores(useItemsStore),
		itemId() {
			return this.$route.params.id;
		},
		fetchedItem() {
			if (this.createNew) {
				return false;
			} else {
				return this.itemsStore.getItemById(this.itemId);
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
