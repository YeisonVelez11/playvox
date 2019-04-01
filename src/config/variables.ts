export const VARIABLES:any = {
    datepicker: {
    // Strings and translations
    dayLabels: {su: 'Dom', mo: 'Lun', tu: 'Mar', we: 'Mie', th: 'Jue', fr: 'Vie', sa: 'Sab'},
    dayLabelsFull: {su: "Domingo", mo: "Lunes", tu: "Martes", we: "Miércoles", th: "Jueves", fr: "Viernes", sa:
    "Sabado"},
    monthLabels: { 1: 'Ene', 2: 'Feb', 3: 'Mar', 4: 'Abr', 5: 'May', 6: 'Jun', 7: 'Jul', 8: 'Ago', 9: 'Sep', 10:
    'Oct', 11: 'Nov', 12: 'Dic' },
    monthLabelsFull: { 1: "Enero", 2: "Febrero", 3: "Marzo", 4: "Abril", 5: "May", 6: "Junio", 7: "Julio", 8:
    "Agosto", 9: "Septiembre", 10: "Octubre", 11: "Noviembre", 12: "Diciembre" },
    // Buttons
    todayBtnTxt: "Hoy",
    clearBtnTxt: "Borrar",
    closeBtnTxt: "Cerrar",
    // Format
    dateFormat: 'yyyy-mm-dd',
    // First day of the week
    firstDayOfWeek: 'Lun',
    // // Disable dates
    /*disableUntil: {year: 2018, month: 10, day: 1},
    disableSince: {year: 2018, month: 10, day: 31},/*
    disableDays: [{year: 2018, month: 10, day: 3}],*/
    //disableDateRanges: [{begin: {year: 2018, month: 10, day: 5}, end: {year: 2018, month: 10, day: 7}}],
    disableWeekends: false,
    // Enable dates (when disabled)
    // Year limits
    minYear: 2017,
    maxYear: 9999,
    // Show Today button
    showTodayBtn: true,
    // Show Clear date button
    showClearDateBtn: true,
    markCurrentDay: true,
    //markDates: [{dates: [{year: 2018, month: 10, day: 20}], color: '#303030'}],
    markWeekends: undefined,
    disableHeaderButtons: false,
    showWeekNumbers: false,
    height: '100px',
    width: '90%',
    selectionTxtFontSize: '15px'
    },
    oEstado_solicitud:
    {
        "Sin Iniciar":{
          "color": "bola_roja"
        },
        "En Ejecución":{
          "color": "bola_amarilla"
        },
        "Terminado":{
          "color": "bola_verde"
        }               
    },
    oEstadoPrioridad:{
        "alta": "bola_roja",
        "media": "bola_amarilla",
        "baja": "bola_gris"
    }
  };
