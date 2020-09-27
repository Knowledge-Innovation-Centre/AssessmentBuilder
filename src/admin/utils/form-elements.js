import randomValueHex from "./helpers"

export default  [
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Text input',
      type: 'text',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'TextInput',
      hidden: false,
      class: '',
      placeholder: '',
      includeInAssessmentTitle: false,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'First and last name',
      type: 'first_last_name',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'TextInput',
      hidden: false,
      class: '',
      placeholder: '',
      includeInAssessmentTitle: true,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Textarea input',
      type: 'textarea',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'TextareaInput',
      hidden: false,
      class: '',
      placeholder: '',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Select input',
      type: 'select',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'SelectInput',
      options: [],
      hidden: false,
      class: '',
      placeholder: '',
      includeInAssessmentTitle: false,
      multiple: false
    },
  {
    key: randomValueHex(15),
    reportItemKey: "",
    name: 'Date input',
    type: 'date',
    defaultValue: null,
    label: '',
    showIf: {
      field: null,
      value: null,
    },
    required: false,
    canRemove: true,
    component: 'DateInput',
    hidden: false,
    class: '',
    placeholder: '',
  },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Radio input',
      type: 'radio',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'RadioInput',
      options: [],
      hidden: false,
      class: '',
      includeInAssessmentTitle: false,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Radio grid input',
      type: 'radio_grid',
      defaultValue: null,
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      required: false,
      canRemove: true,
      component: 'RadioGridInput',
      optionsHorizontal: [],
      optionsVertical: [],
      hidden: false,
      class: '',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'File upload',
      type: 'file_upload',
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      component: 'FileUpload',
      canRemove: true,
      hidden: false,
      class: '',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Page',
      type: 'page',
      showIf: {
        field: null,
        value: null,
      },
      component: 'Page',
      canRemove: true,
      items: [],
      class: '',
      nextButtonText: '',
      previousButtonText: '',
      hidden: false,
      showTitle: false,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Row',
      type: 'row',
      showIf: {
        field: null,
        value: null,
      },
      component: 'Row',
      canRemove: true,
      items: [],
      hidden: false,
      class: '',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Column',
      type: 'column',
      showIf: {
        field: null,
        value: null,
      },
      component: 'Column',
      canRemove: true,
      items: [],
      hidden: false,
      class: '',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Paragraph',
      type: 'paragraph',
      label: '',
      showIf: {
        field: null,
        value: null,
      },
      component: 'Paragraph',
      canRemove: true,
      hidden: false,
      class: '',
    },
  ]



