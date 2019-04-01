import { OnInit ,Component, Input, Output, EventEmitter  } from '@angular/core';
import { ServicesProvider } from '../../providers/services';
import { Router } from '@angular/router';

@Component({
  selector: 'menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {

  constructor(
    private ServicesProvider: ServicesProvider,
    private router : Router
) {
    
  }

  ngOnInit() {
  }

}
