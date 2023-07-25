<template>
	<div>
    <Draggable v-model="medium.fields"
			:animation="200"
			tag="ul"
			handle=".field__drag-handle"
			@start="isDragging = true"
			@end="isDragging = false"
			@change="fieldsOrderChanged">
			<Fields :is="FieldTypes[field.type].component"
				v-for="field in medium.fields"
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
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import Draggable from "vuedraggable";

import { useMediumsStore } from "../store/mediums";
import FieldTypes from "../models/FieldTypes";
import Field from "../components/Fields/Field";
import ListField from "../components/Fields/ListField";
import ShortTextField from "../components/Fields/ShortTextField";
import LongTextField from "../components/Fields/LongTextField";
import DateField from "../components/Fields/DateField";

const store = useMediumsStore();

const router = useRouter();
const route = useRoute();

const medium = ref(store.getMediumById(route.params.id));

onMounted(() => {
  console.log(medium);
})
</script>
