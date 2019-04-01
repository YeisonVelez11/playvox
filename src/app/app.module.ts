import { BrowserModule } from '@angular/platform-browser';

//import { MDBBootstrapModulesPro,MDBSpinningPreloader } from 'ng-uikit-pro-standard';

import { MDBBootstrapModulesPro } from '../../projects/ng-uikit-pro-standard/src/lib/mdb.module';


/*
import { MDBBootstrapModulesPro } from '../../projects/ng-uikit-pro-standard/src/lib/mdb.module';

import { MDBSpinningPreloader } from '../../projects/ng-uikit-pro-standard/src/lib/pro/mdb-pro.module';
*/

import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
//import {ToastModule, ToastService} from '../../projects/ng-uikit-pro-standard/src/lib/pro/alerts';
import { NotFoundComponent,MenuComponent,DashboardComponent} from '../pages/index.paginas';
import { ServicesProvider } from '../providers/services';
import { routing, appRoutingProviders } from './app.routing'
import { HttpModule } from '@angular/http';
import { DatePipe } from '@angular/common';

;
@NgModule({
  declarations: [
    AppComponent,
    MenuComponent,
    NotFoundComponent,
    DashboardComponent

  ],
  imports: [
    BrowserModule,
    MDBBootstrapModulesPro.forRoot(),
    //ToastModule.forRoot(),
    FormsModule,
    ReactiveFormsModule,
    BrowserAnimationsModule,
    HttpModule,
    routing
  ],
  entryComponents: [  ],
  providers: [
  //MDBSpinningPreloader, 
    //ToastService,
    DatePipe,
    ServicesProvider,
    appRoutingProviders
  ],
  bootstrap: [AppComponent]
  
})
export class AppModule { }
