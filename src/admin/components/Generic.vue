<template>

    <fieldset class="generic">
        <legend>{{ form.name }}</legend>

        <common-options v-if="form.type !== 'form'" :object="form" @change="updateObject($event)" />

        <component :is="form.component" :object="form" class="inner"></component>
        <slot/>
    </fieldset>
</template>

<script>
  import Row from "./Row.vue";
  import Column from "./Column.vue";
  import Page from "./Page.vue";
  import Paragraph from "./Paragraph.vue";
  import TextInput from "../form-elements/TextInput.vue";
  import DateInput from "../form-elements/DateInput.vue";
  import TextareaInput from "../form-elements/TextareaInput.vue";
  import RadioInput from "../form-elements/RadioInput.vue";
  import RadioGridInput from "../form-elements/RadioGridInput.vue";
  import SelectInput from "../form-elements/SelectInput.vue";
  import Form from "../form-elements/Form.vue";
  import CommonOptions from "../form-elements/CommonOptions.vue";

  export default {
    name: "Generic",
    props: {
      form: {
        type: Object,
        required: true
      }
    },
    components: {
      Row,
      Column,
      Page,
      Paragraph,
      SelectInput,
      DateInput,
      TextareaInput,
      TextInput,
      RadioInput,
      RadioGridInput,
      Form,
      CommonOptions,
    },
    methods: {
      updateObject(object) {
        console.log(object);
      },
    }
  };
</script>

<style scoped>
    .generic {
        flex: 1;
        display: flex;
        align-items: stretch;
        margin: 10px;
        border: lightgray solid 1px;
        padding: 10px;
        position: relative;
    }

    .inner {
        width: 100%;
        height: 100%;
    }
</style>
