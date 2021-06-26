<template>
	<div>
		<p>{{ $route.params.id }}</p>
		<p>{{ createNew }}</p>
		<p>{{ thisTitle }}</p>
		<input v-model="thisTitle">

		<Draggable
			:animation="200"
			tag="ul"
			handle=".question__drag-handle"
			@start="isDragging = true"
			@end="isDragging = false"
            v-model="thisFields">
			<Questions
				:is="answerTypes[field.type].component"
				v-for="field in thisFields"
				ref="questions"
				:answer-type="answerTypes[field.type]"
				:is-required="false"
				:options="{}"
				v-bind.sync="field"
				@delete="deleteQuestion(question)" />
		</Draggable>

        <a class="button" v-on:click="saveNew()">
            <span class="icon icon-add"></span>
            <span>{{ t('biblio', 'Save') }}</span>
        </a>
	</div>
</template>

<script>
import debounce from 'debounce'
import Draggable from 'vuedraggable'

import answerTypes from '../models/AnswerTypes'
import Question from '../components/Questions/Question'
import QuestionLong from '../components/Questions/QuestionLong'
import QuestionMultiple from '../components/Questions/QuestionMultiple'
import QuestionShort from '../components/Questions/QuestionShort'

import { mapState } from 'vuex'

export default {
	components: {
		Draggable,
		Question,
		QuestionLong,
		QuestionShort,
		QuestionMultiple,
	},
	props: {
		createNew: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			newTitle: '',
            newFields: [
				{
					type: 'short',
					title: 'baum',
					value: '',
				},
				{
					type: 'long',
					title: 'long baum',
					value: '',
				},
				{
					type: 'multiple',
					title: 'multiple baum',
					value: [],
				},
			],
			answerTypes,
			isLoadingQuestions: false,
			isDragging: false,
		}
	},
	computed: {
		...mapState({
			mediums: state => state.mediums.mediums,
		}),
		thisTitle: {
			get() {
				if (this.createNew) {
					return this.newTitle
				} else {
					return this.$store.getters.getMediumById(this.$route.params.id).title
				}
			},
			set(value) {
				if (this.createNew) {
					this.newTitle = value
				} else {
				    this.$store.dispatch('updateMediumTitle', { id: this.$route.params.id, title: value })
				}
			},
		},
        thisFields: {
			get() {
				if (this.createNew) {
					return this.newFields
				} else {
					return this.$store.getters.getMediumById(this.$route.params.id).fields
				}
			},
			set(value) {
				if (this.createNew) {
					this.newFields = value
				} else {
				    this.$store.dispatch('updateMediumFields', { id: this.$route.params.id, fields: value })
				}
			},
		},
	},
	methods: {
		saveNew() {
            this.$store.dispatch('createMedium', { title: this.newTitle, fields: JSON.stringify(this.newFields) })
		},
	},
}
</script>
