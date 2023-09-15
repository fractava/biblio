<template>
    <div class="tableRow">
        <div>
            <slot name="left"></slot>
        </div>
        <div>
            <slot name="right"></slot>
        </div>
    </div>
</template>

<style>
.fieldsTable > .tableRow {
	display: contents;
}

.fieldsTable > .tableRow > div {
	border: var(--color-text-maxcontrast) 2px solid;
    margin: -2px 0 0 -2px;
    padding: 5px;
}
</style>
