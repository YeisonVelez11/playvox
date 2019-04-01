import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions } from '@angular/http';
import { URL } from '../config/webservices';
import { VARIABLES } from '../config/variables';
import { DatePipe } from '@angular/common';
import { Router } from '@angular/router';

//uso
//import { ServicesProvider } from '../../providers/services';

@Injectable()
export class ServicesProvider {
  protected url = URL;
  bSuperPreload:boolean=false;

  constructor(
    private http: Http,
    private datePipe: DatePipe,
    private router : Router
  ) {
  }

  //tener en el component
  //import { IMyOptions } from 'ng-uikit-pro-standard';
  //public myDatePickerOptions: IMyOptions;
  //<mdb-date-picker name="mydate" [options]="myDatePickerOptions" [placeholder]="'Selected date'" [(ngModel)]="model"
    //required></mdb-date-picker>
  //this.myDatePickerOptions=this.ServicesProvider.getDatepickerOpt("posterior");
  //fin_fecha:  'posterior' => valida que no se puedan seleccionar fechas posteriores
  //fin_fecha:  null => no valida que no se puedan seleccionar fechas posteriores
  getDatepickerOpt(fin_fecha:any){

    if(fin_fecha=="posterior"){
      var fechaManana:any = new Date().setDate( new Date().getDate() + 1 );
      fechaManana=this.datePipe.transform(fechaManana, 'yyyy/MM/dd');
      fechaManana=fechaManana.split("/");
      VARIABLES.datepicker["maxYear"]=fechaManana[0];
      VARIABLES.datepicker["disableSince"]= {year: fechaManana[0], month: fechaManana[1], day: fechaManana[2]};
    }
    return VARIABLES.datepicker;
  }

  fn_sesion(){
    if(!localStorage.getItem("nombre")){
      console.log("se va")
       return this.router.navigate(["/"]);
    }
  }
  //cuenta cuantos dias restan pasada una fecha y la cantidad de dias
  getDaysRemaining(dias:any,fecha:any){
    let fechaRecibida:any=new Date(fecha);

    let hoy:any = new Date();
    let fecha_mas_dias:any=fechaRecibida.setDate(fechaRecibida.getDate() + parseInt(dias)+1);
    fecha_mas_dias=new Date(this.datePipe.transform(fecha_mas_dias, 'yyyy/MM/dd'));
    let _MS_PER_DAY = 1000 * 60 * 60 * 24;
    let utc1 = Date.UTC(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
    let utc2 = Date.UTC(fecha_mas_dias.getFullYear(), fecha_mas_dias.getMonth(), fecha_mas_dias.getDate());
    return (Math.floor((utc2 - utc1) / _MS_PER_DAY));
  }

  getCurrentYear(){
    let aAnios=[];
    let aAnioActual;
    for(let i=2017; i<=new Date().getFullYear(); i ++){
      aAnios.push(i);
    }

    aAnioActual=(new Date()).getFullYear();
    return { "aAnios":aAnios, "iAnioActual":aAnioActual};
  }

  //yyyy-mm-dd
  //si se quiere restar dias entre 2 fechsa incluir el primer argumento, asi como esta solo resta desde el dia actual a una fecha
  countDays(date1:any, date2:any)
  {
      let _MS_PER_DAY = 1000 * 60 * 60 * 24;
      // por defecto se resta desde el dia actual
      let hoy:any = new Date();
      let utc1 = Date.UTC(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
      let utc2 = Date.UTC(date2.getFullYear(), date2.getMonth(), date2.getDate());
      return (Math.floor((utc2 - utc1) / _MS_PER_DAY));
  }



  /*
    esta funcion genera un popup dinámico y además permite llamar funciones
    genera un popup genérico.
    titulo: titulo del modal, de ahi se depende el color ej: 'Éxito', 'error', 'info'
    cuerpo: cuerpo del modal

    estos parametros son para llamar una funcion contenida en el mismo componente

    funcion: nombre de la funcion en string
        scope: enviar  this

    param: parametros en cadena
    this.ServicesProvider.generarPopupGenerico("Advertencia", "¿Desea eliminar este avance del entregable","fn_deleteDetalleEntregable",this,id);
  }
  */
  generarPopupGenerico(titulo:string, cuerpo:string,funcion?:any,scope?:any,param?:any){
    /*this.modalRef =  this.modalService.show(ModalgenericoComponent);
    this.modalRef.content.title = titulo;
    this.modalRef.content.body = cuerpo;
    this.modalRef.content.component=scope;
    this.modalRef.content.funcion=funcion;
    this.modalRef.content.param=param;

    if(titulo.toLowerCase()=="exito" || titulo.toLowerCase()=="éxito"){
      this.modalRef.content.estilo="success";
    }
    else if(titulo.toLowerCase()=="error" ){
      this.modalRef.content.estilo="danger";
    }
    else{
      this.modalRef.content.estilo="warning";
    }*/
  }

  //retorna el icono de la extension
  fn_getIconMedia(extension:any){
    extension=extension.toLowerCase();
    extension=extension.split(".");
    extension=extension[extension.length-1];
    if(extension=="jpg" || extension=="jpeg" || extension=="png" || extension=="gif" || extension=="bmp"){
       return  "fa-file-image-o";
    }
    else if(extension=="pdf"){
      return "fa-file-pdf-o";
    }
    else if(extension=="doc" || extension=="docx"){
      return "fa-file-word-o";
    }
    else if(extension=="ppt" || extension=="pptx"){
      return "fa-file-powerpoint-o";
    }
    else if(extension=="xls" || extension=="xlxs"){
      return "fa-file-powerpoint";
    }
    else{
      return "fa-file";
    }
  }

  //bSuperPreload si es true el preloadOff también debe ser true, con esto se garantiza que ningun preloader sin el parametro true lo pueda cerrar
  preloaderOn(bSuperPreload?:boolean){
    if(bSuperPreload){
      this.bSuperPreload=bSuperPreload;
    }
    if(document.querySelector('#preloader')){
      document.querySelector('#preloader')!.classList.remove("hide_preloader");
      document.querySelector('#preloader')!.classList.add("show_preloader");
    }
    else{
      var d1 = document.querySelector('body');
      d1!.insertAdjacentHTML('beforeend', '<div  id="preloader"><div class="showbox"><div class="loader1"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg><div class="cargando">Cargando...</div></div></div><div class="preloader view" style="height:100vh; width:100vw; position:fixed; top:0; left:0; z-index:9999 !important; opacity:0.5; background:black;"><div class="flex-center"></div></div></div>');
    }
  }
  preloaderOff(bSuperPreload?:boolean){
    console.log(this.bSuperPreload);
    if(bSuperPreload){
      this.bSuperPreload=false;
    }
    if(!this.bSuperPreload){
      console.log("se lo quito");
      if(document.querySelector('#preloader')){
        document.querySelector('#preloader')!.classList.remove("show_preloader");
        document.querySelector('#preloader')!.classList.add("hide_preloader");      
      }
    }
  }
  //define color en escala de 1 a 100
  estadoSemaforo(valor:any){
    if(valor>=99){
      return "background_verde";
    }
    else if(valor>=20 && valor<= 44){
      return "background_rojo_amarillo";
    }

    else if(valor>=45 && valor<= 70){
      return "background_amarillo";
    }
    else if(valor>=71 && valor<99){
      return "background_amarillo_verdoso";
    }

    else{
      return "background_rojo";
    }     
  }
  //define color en escala de 1 a 100
    getColorestadoSemaforo(valor:any){
    if(valor==100){
      return {"color": "a8e063", "class":"background_verde"};
    }
    else if(valor>20 && valor<=65){
      return  {"color": "ff4800", "class":"background_naranja"};
    }

    else if(valor>=66 && valor<= 99){
      return  {"color": "c79810", "class":"background_amarillo"};
    }
    else{
      return {"color": "ed213a", "class":"background_rojo"};
    }     
  }

  //define color segun diferencia de dias para entregables
  estadoSemaforoDia(dia:any){
    if(dia>=31){
      return "background_verde";
    }
    else if(dia>11 && dia< 30){
      return "background_amarillo";
    }
    else{
      return "background_rojo";
    }     
  }
  //define color segun diferencia de dias para entregables
  estadoSemaforoDiaSolicitudes(dia:any){
    if(dia>=7){
      return "background_verde";
    }
    else if(dia>3 && dia< 6){
      return "background_amarillo";
    }
    else{
      return "background_rojo";
    }     
  }

  alertaOn(titulo_alerta:string, cuerpo_alerta:string){
      if(titulo_alerta.toLowerCase()=='éxito' || titulo_alerta.toLowerCase()=='exito'){
        var clase_titulo="alert-success";
      }
      else if(titulo_alerta.toLowerCase()=='error' ){
        var clase_titulo="alert-danger";
      }
      else{
        var clase_titulo= "alert-warning";
      }

      if(document.querySelector('#alerta')){
        document.querySelector('#alerta')!.classList.remove("ocultar_animacion");
        document.querySelector('#alerta')!.classList.remove("ocultar");
        document.querySelector('#alerta')!.classList.remove("alert-success");
        document.querySelector('#alerta')!.classList.remove("alert-danger");
        document.querySelector('#alerta')!.classList.remove("alert-alert-warning");
        document.querySelector('#alerta')!.classList.add(clase_titulo);
      }
      else{
        var d1 = document.querySelector('body');
        d1!.insertAdjacentHTML('beforeend', '  <div #alert id="alerta" onclick="cerrarAlerta()" style="position:fixed; width:80%; top:0px; right:0px; z-index: 999999999 !important;" class="alert '+clase_titulo +' alert-dismissible fade show animated fadeIn" role="alert"><button type="button" class="close" aria-label="Close" (click)="closeAlert()"><span aria-hidden="true">&times;</span></button><strong id="titulo_alerta">*</strong> <span id="cuerpo_alerta">*</span>.</div>');
      }
      document.getElementById("titulo_alerta")!.innerHTML =titulo_alerta;
      document.getElementById("cuerpo_alerta")!.innerHTML =cuerpo_alerta;
      this.alertaOff();

  }

  alertaOff(){
      setTimeout(()=>{
        document.querySelector('#alerta')!.classList.add("ocultar_animacion");
        setTimeout(()=>{
          document.querySelector('#alerta')!.classList.add("ocultar");
        },3000)
      },2000)
  }


  //this.ServicesProvider.post(SERVICES.CONSULTAR_INFO, query,true/false)
  //is_file determina si el formulario envia un archivo
  public post(inUrl: string, query?: any,is_file?:boolean): Promise<any> {
    console.log("post")
    return new Promise((resolve, reject) => {
 
      var headers;
      if(!is_file){
        headers = new Headers({
          'Content-Type': 'application/json'
        });        

      }
      else{
        headers = new Headers({
        });        
      }

      const options = new RequestOptions({
        headers: headers
      });
      this.http.post(this.url +  inUrl, query, options)
      .subscribe(response => {
        try {
          response = response.json();
          resolve(response);
        } catch (error) {
          console.log('[api-274]', response);
          reject(response);
        }
      }, fail => {
        try {
          fail = fail.json();
        } catch (error) {
          console.log('[api-108]', fail);
        }
        reject(fail);
      });
    });
  }
  //this.ServicesProvider.get("https://reqres.in/api/users", {"page":2}).then((data) => {

  public get(inUrl: string, params: any): Promise<any> {
    //const formedUrl = this.filterAccents(inUrl.split(' ').join('-'));
    return new Promise((resolve, reject) => {
      const headers = new Headers({
        'Content-Type': 'application/json'
      });
      console.log(params);
      const options = new RequestOptions({
        headers: headers,
        params: params
      });
      this.http.get(this.url + inUrl, options)
      .subscribe(response => {
        try {
          response = response.json();
          resolve(response);
        } catch (error) {
          console.log('[api-274]', response);
          reject(response);
        }
      }, fail => {
        try {
          fail = fail.json();
        } catch (error) {
          console.log('[api-162]', fail);
        }
        reject(fail);
      });
    });
  }

  public delete(inUrl: string): Promise<any> {
    const formedUrl = inUrl.split(' ').join('-');
    return new Promise((resolve, reject) => {
      const headers = new Headers({
        'Content-Type': 'application/json'
      });
      const options = new RequestOptions({
        headers: headers
      });
      this.http.delete(`${this.url}${formedUrl}`, options)
      .subscribe(response => {
        try {
          response = response.json();
          resolve(response);
        } catch (error) {
          console.log('[api-274]', response);
          reject(response);
        }
      }, fail => {
        try {
          fail = fail.json();
        } catch (error) {
          console.log('[api-227]', fail);
        }
        reject(fail);
      });
    });
  }

  public put(inUrl: string, query?: any): Promise<any> {
    return new Promise((resolve, reject) => {
      const headers = new Headers({
        'Content-Type': 'application/json'
      });
      const options = new RequestOptions({
        headers: headers
      });
      this.http.put(`${this.url}${inUrl}`, query, options)
      .subscribe(response => {
        try {
          response = response.json();
          resolve(response);
        } catch (error) {
          console.log('[api-274]', response);
          reject(response);
        }
      }, fail => {
        try {
          fail = fail.json();
        } catch (error) {
          console.log('[api-284]', fail);
        }
        reject(fail);
      });
    });
  }
}
