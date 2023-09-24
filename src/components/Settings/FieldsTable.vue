<template>
    <table class="fieldsTable">
        <colgroup>
            <col>
            <col>
            <col>
            <col span="2" style="width: 50%;">
        </colgroup>
        <slot></slot>
    </table>
</template>

<style>
.fieldsTable {
    width: 100%;
}

.fieldsTable tr:hover, .fieldsTable tr:focus, .fieldsTable tr:active {
    background-color: transparent;
}
</style>