import {Component, ElementRef, Input} from '@angular/core';
import {ISelectedOption} from '../interfaces/selected-option.interface';

@Component({
  selector: 'mdb-option',
  templateUrl: 'mdb-option.component.html'

})

export class MdbOptionComponent {
  @Input() value: string;
  clicked = false;
  selectedItem: ISelectedOption;

  constructor(public el: ElementRef) {
    this.clicked = false;
  }

  handleMouseDown(event: any) {
    const text = this.value || event.target.text || event.target.textContent || event.target.value;
    this.selectedItem = {text: text.toString().trim(), element: this};
    this.clicked = true;
  }

}
