import { Component,OnInit,ViewChild } from '@angular/core';
import { ServicesProvider } from '../../providers/services';
import  {SERVICES } from '../../config/webservices';
import { VARIABLES } from '../../config/variables';
import { FormControl,FormGroup, Validators,FormArray, FormBuilder } from '@angular/forms';
import { BreadcrumbModule } from 'projects/ng-uikit-pro-standard/src/public_api';

@Component({
  selector: 'dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  filter:any;
  aData:any;
  aDataAux:any;
  aHeaders:any;
  aFiltros:any;
  oCurrentFilter:any;
  option_filter:any;
  validationForm: FormGroup;
  bCheckbox:boolean=true;
  aFiltersSelected=[];
  numberRequests:number=0;
  oDictionaryoperators=
  {
    "equal":"Equal",
    "not_equal":"Not Equal",
    "lt":"Less than",
    "gt":"Greater than",
    "lte":"less than or equal to",
    "gte":"Greater than or equal to",
    "like": "Like"
  }
  oDictionaryBall=
  {
    "High":"background_bola_roja",
    "Medium":"background_bola_amarilla",
    "Low":"background_bola_gris"
  }

  @ViewChild('frame') frame: any;
  constructor(
    private ServicesProvider: ServicesProvider,public fb: FormBuilder
) {

    this.validationForm = fb.group({
      emailFormEx: [null, [Validators.required]],
      option_filter:[null, [Validators.required]]
    });
  }

  ngOnInit() {
    this.fn_getData();
    this.fn_getFilters();
  }

  fn_getData(){
      this.ServicesProvider.get(SERVICES.GET_DATA, {}).then(data=>{
        this.numberRequests=data.length;
        this.aData=data.slice(0);
        this.aDataAux=data.slice(0);
        this.aHeaders=Object.keys(this.aData[0])
        let auxHeader=[];
        for(var i in this.aHeaders){
          auxHeader.push({ "name":this.aHeaders[i],"show":true})
        }
        this.aHeaders=auxHeader;
        auxHeader=null;
      });      
    }

  fn_getFilters(){
    this.ServicesProvider.get(SERVICES.GET_FILTERS, {}).then(data=>{
      this.aFiltros=data;
      this.filter=this.aFiltros[0];
      for(var i in this.aFiltros){
        this.aFiltros[i]["index"]=parseInt(i);
      }
    });      
  }

  fn_showHide(name){
    var show=false;
    for(var i in this.aHeaders){
      if(this.aHeaders[i].name.toLowerCase()==name.toLowerCase()){
        this.aHeaders[i].show=!this.aHeaders[i].show
        show=this.aHeaders[i].show;
        break;
      }

    }
    for(var i in this.aFiltros){
      if(this.aFiltros[i].label.toLowerCase()==name.toLowerCase()){      
        this.aFiltros[i].show=show
        break;
      }
    }
  }

  fn_saveFilter(){
    if(!this.aFiltersSelected.includes(this.filter.index)){
      this.aFiltersSelected.push(this.filter.index);
    }
    this.filter["field_selected"]=[];
    for(var i in this.filter.values){
      this.filter.values[i].select=this.validationForm.controls["checkboxes"].value[i];
      if(this.filter.values[i].select){
        this.filter["field_selected"].push(this.filter.values[i].name);
      }
    }
    //para los radio button opciones al stilo equal not_equal
    this.filter.text=this.validationForm.controls["emailFormEx"].value 
    for(var i in this.filter.operators){
      if(this.filter.operators[i].operator==this.validationForm.controls["option_filter"].value){
        this.filter.operators[i].select=true;
        this.filter["operator_selected"]=this.validationForm.controls["option_filter"].value;
      }
      else{
        this.filter.operators[i].select=false;
      }
    }
    this.fn_filterData();
  }

  fn_deleteFilter(item){
    delete this.aFiltros[item]["field_selected"];
    delete this.aFiltros[item]["operator_selected"];
    for(var i in this.filter.operators){
        this.filter.operators[i].select=false;
    }
    for(var i in this.filter.values){
      this.filter.values[i].select=false;
    }
    this.aFiltros[item]["text"]="";  
    this.aFiltersSelected.splice(this.aFiltersSelected.indexOf(item),1);
    this.fn_filterData();
  }

  fn_filterData(){
    this.aData=this.aDataAux.slice(0);
    var aDataAux=this.aData.slice(0);
    var aIndex=[];
    var operador_seleccionado=false;
     for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){
            if(this.aFiltros[j].operator_selected=="equal"){
              operador_seleccionado=true;              
              if(this.aFiltros[j].values.length!=0){
                  if(!this.aFiltros[j].field_selected.includes(aDataAux[i][this.aFiltros[j].label])){
                    let index=this.aData.indexOf(aDataAux[i]);
                    aIndex.push(index);
                  }
              }
              else{
                let data=isNaN(aDataAux[i][this.aFiltros[j].label])?aDataAux[i][this.aFiltros[j].label].toLowerCase():parseInt(aDataAux[i][this.aFiltros[j].label]);
                let texto=isNaN(this.aFiltros[j].text)?this.aFiltros[j].text.toLowerCase():parseInt(this.aFiltros[j].text);
                if(data!=texto){
                  let index=this.aData.indexOf(aDataAux[i]);
                  aIndex.push(index);
                }              
              }
            }
          }
        }
      }
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }     
      aDataAux=this.aData.slice(0);
      var aIndex=[];
      var operador_seleccionado=false;      
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){
            if(this.aFiltros[j].operator_selected=="not_equal"){
              operador_seleccionado=true;
              if(this.aFiltros[j].values.length!=0){
                if(this.aFiltros[j].field_selected.includes(aDataAux[i][this.aFiltros[j].label])){
                    let index=this.aData.indexOf(aDataAux[i]);
                    this.aData.splice(index,1);
                }
              }
              else{
                let data=isNaN(aDataAux[i][this.aFiltros[j].label])?aDataAux[i][this.aFiltros[j].label].toLowerCase():parseInt(aDataAux[i][this.aFiltros[j].label]);
                let texto=isNaN(this.aFiltros[j].text)?this.aFiltros[j].text.toLowerCase():this.aFiltros[j].text;
                if(data==texto){
                  let index=this.aData.indexOf(aDataAux[i]);
                  aIndex.push(index);
                }
              }
            }            
          }
        }
      }
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }    
      aDataAux=this.aData.slice(0);
      operador_seleccionado=false;
      var aIndex=[];
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){
            if(this.aFiltros[j].operator_selected=="lt"){
              operador_seleccionado=true;
              let data=parseInt(aDataAux[i][this.aFiltros[j].label]);
              let numero=parseInt(this.aFiltros[j].text);
              if(data>=numero){
                let index=this.aData.indexOf(aDataAux[i]);
                aIndex.push(index);
              }
            }
            
          }
        }
      }  
       if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }         
      this.aData=this.aData.splice(0)
      var aIndex=[];
      var operador_seleccionado=false;
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){

            if(this.aFiltros[j].operator_selected=="lte"){
              operador_seleccionado=true;
              let data=parseInt(aDataAux[i][this.aFiltros[j].label]);
              let numero=parseInt(this.aFiltros[j].text);
              if(data<numero){
                let index=this.aData.indexOf(aDataAux[i]);
                aIndex.push(index);
              }
            }            
          }
        }
      } 
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }
      aDataAux=this.aData.slice(0);
      var aIndex=[];
      var operador_seleccionado=false;
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){
            if(this.aFiltros[j].operator_selected=="gt"){
              operador_seleccionado=true
              let data=parseInt(aDataAux[i][this.aFiltros[j].label]);
              let numero=parseInt(this.aFiltros[j].text);
              if(data<=numero){
                let index=this.aData.indexOf(aDataAux[i]);
                aIndex.push(index);         
              }
            }            
          }
        }
      }      
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }
      aDataAux=this.aData.slice(0);
      var aIndex=[];
      var operador_seleccionado=false;
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){

            if(this.aFiltros[j].operator_selected=="gte"){
              operador_seleccionado=true;
              let data=parseInt(aDataAux[i][this.aFiltros[j].label]);
              let numero=parseInt(this.aFiltros[j].text);
              if(data<numero){
                let index=this.aData.indexOf(aDataAux[i]);
                aIndex.push(index);       
              }
            }            
          }
        }
      }      
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }
      aDataAux=this.aData.slice(0);
      var aIndex=[];
      var operador_seleccionado=false;
      for(var i in aDataAux){
        for(var j in this.aFiltros){
          if(this.aFiltros[j].operator_selected){

            if(this.aFiltros[j].operator_selected=="like"){
              operador_seleccionado=true;
              let data=aDataAux[i][this.aFiltros[j].label].toLowerCase();
              let texto=this.aFiltros[j].text.toLowerCase();

              if(data.search(texto)==-1 ){
                let index=this.aData.indexOf(aDataAux[i]);
                aIndex.push(index);         
              }
            }            
          }
        }
      }      
      if(operador_seleccionado){
        for(var n = aIndex.length -1; n >= 0 ; n--){
          this.aData.splice(aIndex[n], 1);
        }        
      }
      this.numberRequests=this.aData.length;
  }

  fn_setFilter(item){
    //reseteando los campos del modal
    this.validationForm.reset();
    this.bCheckbox=true;
    setTimeout(()=>{
      //asignando los campos del modal
      this.validationForm.controls["emailFormEx"].setValue(this.filter.text);
      for(var i in this.filter.operators){
        if(this.filter.operators[i].select){
          this.filter.operators[i].select=true;
          this.validationForm.controls["option_filter"].setValue(this.filter.operators[i].operator);
          break;
        }
      }
    })
    if( this.filter.values.length!=0){
      var checkboxControls = this.filter.values.map(control => new FormControl(control.select));
      this.validationForm.setControl('checkboxes', new FormArray(checkboxControls));
      this.validationForm.controls["emailFormEx"].setValue("");
      this.validationForm.get('emailFormEx')!.clearValidators();
      this.validationForm.get('emailFormEx')!.updateValueAndValidity(); 
    }
    else{
      this.validationForm.get('emailFormEx')!.setValidators([Validators.required]);
      this.validationForm.get('emailFormEx')!.updateValueAndValidity();            
    }    
    this.frame.show();
  }

  fn_submit(formGroup: FormGroup) {
    if (formGroup.valid) {
      if(this.filter.values.length!=0){
        var numcheckbox=0;
        for(var i in this.validationForm.controls["checkboxes"].value){
          if(this.validationForm.controls["checkboxes"].value[i]){
            numcheckbox++;
          }
        }
        //validar que se seleccione por lo menos una opción del checkbox
        this.bCheckbox=numcheckbox>0?true:false;
        if(this.bCheckbox){
          this.fn_saveFilter();
          this.frame.hide();  
        }
      }
      else{
        this.fn_saveFilter();
        this.frame.hide();  
      }
    } else {
      this.validateAllFormFields(formGroup); //{7}
    }
  }

  validateAllFormFields(formGroup: FormGroup) {         
    Object.keys(formGroup.controls).forEach(field => {  
      const control = formGroup.get(field);             
      if (control instanceof FormControl) {             
        control.markAsTouched({ onlySelf: true });
      } else if (control instanceof FormGroup) {        
        this.validateAllFormFields(control);            
      }
    });
  }

}
