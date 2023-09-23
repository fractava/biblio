<template>
	<div>
		<p>ID: {{ $route.params.id }}</p>
		<p>createNew: {{ createNew }}</p>

		<ShortTextField :field-type="FieldTypes['short']"
			:allow-title-edit="false"
			:allow-deletion="false"
			:enable-drag-handle="false"
			:is-required="true"
			:options="{}"
			:title="t('biblio', 'Title')"
			:value.sync="thisTitle" />

		<Draggable v-model="thisFields"
			:animation="200"
			tag="ul"
			handle=".field__drag-handle"
			@start="isDragging = true"
			@end="isDragging = false"
			@change="fieldsOrderChanged">
			<Fields :is="FieldTypes[field.type].component"
				v-for="field in thisFields"
				:key="field.title + '-field'"
				ref="fields"
				:field-type="FieldTypes[field.type]"
				:is-required="false"
				:options="{}"
				:title.sync="field.title"
				:value="field.value"
				@update:value="(newValue) => onFieldUpdate(newValue, field)"
				@delete="deleteField(field)" />
		</Draggable>

		<NcActions ref="addFieldMenu"
			:open.sync="addFieldMenuOpened"
			:menu-title="t('biblio', 'Add a field')"
			:primary="true"
			default-icon="icon-add">
			<NcActionButton v-for="(field, type) in FieldTypes"
				:key="field.label"
				:close-after-click="true"
				:icon="field.icon"
				@click="addField(type, field)">
				{{ field.label }}
			</NcActionButton>
		</NcActions>

		<a v-if="createNew" class="button" @click="saveNew()">
			<span class="icon icon-add" />
			<span>{{ t('biblio', 'Save') }}</span>
		</a>
	</div>
</template>

<script>
import Draggable from "vuedraggable";

import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import NcActions from "@nextcloud/vue/dist/Components/NcActions";

import FieldTypes from "../models/FieldTypes";
import Field from "../components/Fields/Field";
import ListField from "../components/Fields/ListField";
import ShortTextField from "../components/Fields/ShortTextField";
import LongTextField from "../components/Fields/LongTextField";
import DateField from "../components/Fields/DateField";

import { mapStores } from 'pinia';

import { useItemsStore } from "../store/biblio.js";

export default {
	components: {
		Draggable,
		Field,
		ListField,
		ShortTextField,
		LongTextField,
		DateField,
		NcActionButton,
		NcActions,
	},
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			newTitle: "",
			newFields: [
				{
					type: "short",
					title: "baum",
					value: "",
				},
				{
					type: "long",
					title: "long baum",
					value: "",
				},
				{
					type: "multiple",
					title: "multiple baum",
					value: [
						{
							id: "wblyp",
							text: "t",
						},
						{
							id: "nnwfq",
							text: "test",
						},
						{
							id: "bikng",
							text: "baum",
						},
					],
				},
				{
					type: "date",
					title: "date baum",
					value: "",
				},
			],
			FieldTypes,
			isDragging: false,
			addFieldMenuOpened: false,
			thisTitle: "",
			thisFields: [],
		};
	},
	watch: {
		thisTitle(value) {
			if (!this.createNew) {
				this.$store.dispatch("updateItemTitle", { id: this.$route.params.id, title: value });
			}
		},
	},
	mounted() {
		if (this.createNew) {
			this.thisTitle = this.newTitle;
			this.thisFields = this.newFields;
		} else {
			this.thisTitle = this.$store.getters.getItemById(this.$route.params.id).title;
			this.thisFields = [];
			this.$store.getters.getItemFields(this.$route.params.id).then((fields) => {
				this.thisFields = fields;
			});
		}
	},
	computed: {
		...mapStores(useItemsStore),
	},
	methods: {
		async saveNew() {
			this.itemsStore.createItem({ title: this.newTitle, fields: this.newFields })
				.then((id) => {
					this.$router.push({
						path: "/item/" + id,
					});
				});
		},
		onFieldUpdate(newValue, field) {
			field.value = newValue;
			console.log(newValue, field);
			this.itemsStore.updateItemField(field);
		},
		addField(type, field) {
			this.thisFields.push({
				title: field.label,
				type,
				value: field.defaultValue,
			});
		},
		deleteField(field) {
			console.log(field);

			this.thisFields = this.thisFields.filter(function(value) {
				return value !== field;
			});

			if (!this.createNew) {
			    this.$store.dispatch("deleteItemField", { itemId: this.$route.params.id, id: field.id });
			}
		},
		fieldsOrderChanged() {
			const newOrder = [];

			for (const field of this.thisFields) {
				newOrder.push(field.id);
			}

			console.log(newOrder);

			this.$store.dispatch("updateItemFieldsOrder", { id: this.$route.params.id, fieldsOrder: JSON.stringify(newOrder) });
		},
	},
};
</script>
