<template>
  <div>
        <label for="label">
          {{ object.label }} <template v-if="object.required">*</template>
            <input id="label" v-model="value" :placeholder="object.placeholder" type="text" >
        </label>

    </div>
</template>

<script>

  export default {

    name: 'TextInput',

    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      value: {
        get () {
          return this.$store.state.assessment[this.object.key]
        },
        set (newValue) {
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue})
        }
      }
    },

    data () {
      return {
      };
    },

    methods: {
      updateObject(object) {
        console.log(object);
      }
    }
  };
</script>
