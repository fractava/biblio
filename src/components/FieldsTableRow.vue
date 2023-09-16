<template>
    <div class="tableRow">
        <!-- Drag handle -->
	    <div v-if="enableDragHandle"
            class="drag-handle"
            :aria-label="t('biblio', 'Drag to reorder the fields')">
            <Drag/>
        </div>
        
        <!-- Left/Title -->
        <div>
            <slot name="left"></slot>
        </div>

        <!-- Right / Value -->
        <div>
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script>
import Drag from 'vue-material-design-icons/Drag.vue';


export default {
    components: {
        Drag,
    },
    props: {
        enableDragHandle: {
            type: Boolean,
            default: true,
        }
    },
}
</script>

<style lang="scss">
.fieldsTable .tableRow {
	display: contents;
}

.fieldsTable .tableRow > div:not(.drag-handle) {
	border: var(--color-text-maxcontrast) 2px solid;
    margin: -2px 0 0 -2px;
    padding: 5px;
}

.drag-handle {
		width: 44px;
		height: 100%;
		opacity: .5;

		&:hover,
		&:focus {
			opacity: 1;
		}
		cursor: grab;

		&:active {
			cursor: grabbing;
		}
	}
</style>
