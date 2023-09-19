<template>
    <tr class="tableRow">
        <!-- Drag handle -->
	    <td class="drag-handle"
            :class="{'drag-handle-active': enableDragHandle}"
            :style="{'opacity': enableDragHandle ? .5 : 0}"
            :aria-label="t('biblio', 'Drag to reorder the fields')">
            <Drag/>
        </td>
        
        <!-- Left/Title -->
        <td>
            <slot name="left"></slot>
        </td>

        <!-- Right / Value -->
        <td>
            <slot name="right"></slot>
        </td>
    </tr>
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
.fieldsTable .tableRow.ghost {
	opacity: 0;
}

/* Custom Border Collapse */
.tableRow td:not(:first-child) {
    background-color: var(--color-main-background);
    padding: 5px;
    border-left: 1px solid;
}
.tableRow td:last-child {
    border-right: 1px solid;
}

.tableRow:not(.ghost) td:not(:first-child), .tableRow:active td:not(:first-child) {
    border-top: 1px solid;
}

tbody:last-child .tableRow:not(.ghost):last-child td:not(:first-child), .tableRow:active td:not(:first-child), .tableRow:has(+ .tableRow.ghost) td:not(:first-child) {
    border-bottom: 1px solid;
}

.drag-handle {
    width: 44px;
    height: 100%;
    transition: opacity 0.5s;
}

.drag-handle-active {
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
