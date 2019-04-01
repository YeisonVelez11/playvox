import {
  AfterContentInit,
  Component,
  ContentChildren,
  ElementRef, HostListener, Input,
  OnInit,
  Renderer2, ViewChild,
} from '@angular/core';
import {MdbOptionComponent} from './mdb-option.component';
import {ISelectedOption} from '../interfaces/selected-option.interface';
import {Observable, Subject} from 'rxjs';

@Component({
  selector: 'mdb-auto-completer',
  templateUrl: 'mdb-auto-completer.component.html',
  exportAs: 'mdbAutoCompleter',
})

export class MdbAutoCompleterComponent implements OnInit, AfterContentInit {
  @Input() textNoResults: string;
  @Input() clearButton = true;
  @Input() clearButtonTabIndex = 0;

  @ContentChildren(MdbOptionComponent, {descendants: true, read: ElementRef}) optionList: Array<any>;
  @ContentChildren(MdbOptionComponent, {descendants: true, read: MdbOptionComponent}) mdbOptions: MdbOptionComponent[];

  @ViewChild('dropdown') dropdown: ElementRef;

  private _allItems: Array<any> = [];
  private _isOpen = false;
  private _selectedItemIndex = -1;
  private _selectedItem: ISelectedOption;
  private _selectedItemChanged: Subject<any> = new Subject<any>();

  constructor(private renderer: Renderer2) {
  }

  @HostListener('mousedown') onItemClick() {
    let selectedElement: MdbOptionComponent = <MdbOptionComponent>{};

    this.mdbOptions.forEach((el: MdbOptionComponent) => {
      if (el.clicked === true) {
        selectedElement = el;
      }
      el.clicked = false;
    });

    this.setSelectedItem({text: selectedElement.value, element: selectedElement});
    this.highlightRow(0);
  }

  public setSelectedItem(item: ISelectedOption) {
    this._selectedItem = item;
    this._selectedItemChanged.next(this.getSelectedItem());
  }

  public getSelectedItem() {
    return this._selectedItem;
  }

  public selectedItemChanged(): Observable<any> {
    return this._selectedItemChanged;
  }

  public isOpen() {
    return this._isOpen;
  }

  public show() {
    this._isOpen = true;
  }

  public hide() {
    this._isOpen = false;
  }

  removeHighlight(index: number) {
    setTimeout(() => {
      this.optionList.forEach((el: any, i: number) => {
        const completerRow = el.nativeElement.querySelectorAll('.completer-row');
        if (i === index) {
          this.renderer.addClass(el.nativeElement.firstElementChild, 'highlight-row');
        } else if (i !== index) {
          completerRow.forEach((elem: any) => {
            this.renderer.removeClass(elem, 'highlight-row');
          });
        }
      });
    }, 0);
  }

  highlightRow(index: number) {
    this._allItems = this.optionList
      .filter(el => el.nativeElement.firstElementChild.classList.contains('completer-row'))
      .map(elem => elem.nativeElement);

    if (this._allItems[index]) {
      this.optionList.forEach((el: any, i: number) => {
        const completerRow = el.nativeElement.querySelectorAll('.completer-row');

        if (index === i) {
          this.removeHighlight(index);
          this.renderer.addClass(completerRow[completerRow.length - 1], 'highlight-row');
        }
      });
    }
    this._selectedItemIndex = index;
  }

  navigateUsingKeyboard(event: any) {
    if (this.dropdown) {
      switch (event.key) {
        case 'ArrowDown':
          this.moveHighlightedIntoView(event.key);
          if (!this.isOpen()) {
            this.show();
          }

          if (this._selectedItemIndex === 0) {
            this.highlightRow(0);
          }

          if (this._selectedItemIndex + 1 <= this._allItems.length - 1) {
            this.highlightRow(++this._selectedItemIndex);
          } else if (this._selectedItemIndex + 1 === this._allItems.length) {
            this.highlightRow(0);
          }
          break;
        case 'ArrowUp':

          this.moveHighlightedIntoView(event.key);
          if (this._selectedItemIndex === -1 || this._selectedItemIndex === 0) {
            this.highlightRow(this._allItems.length);
          }

          this.highlightRow(--this._selectedItemIndex);
          break;
        case 'Escape':
          this.hide();
          break;
        case 'Enter':
          const selectedOption = this.mdbOptions.map(el => el)[this._selectedItemIndex];
          if (selectedOption) {
            this.setSelectedItem({text: selectedOption.value, element: selectedOption});
          }

          this.hide();
          break;
      }
    }

  }

  moveHighlightedIntoView(type: string) {
    let listHeight = 0;
    let itemIndex = this._selectedItemIndex;

    this.optionList.forEach((el: any) => {
      listHeight += el.nativeElement.offsetHeight;
    });

    if (itemIndex > -1) {
      let item: any = null;
      let itemHeight = 0;

      this.optionList.forEach((el: any, i: number) => {
        if (i === itemIndex + 1) {
          item = el.nativeElement;
          itemHeight = item.offsetHeight;
        }
      });

      const itemTop = (itemIndex + 1) * itemHeight;
      const viewTop = this.dropdown.nativeElement.scrollTop;
      const viewBottom = viewTop + listHeight;

      if (type === 'ArrowDown') {
        this.renderer.setProperty(this.dropdown.nativeElement, 'scrollTop', itemTop - itemHeight);
      } else if (type === 'ArrowUp') {
        if (itemIndex === 0) {
          itemIndex = this.optionList.length - 1;
        } else {
          itemIndex--;
        }

        if (itemIndex === this._allItems.length - 2) {
          this.renderer.setProperty(this.dropdown.nativeElement, 'scrollTop', viewBottom - itemHeight);
        } else {
          this.renderer.setProperty(this.dropdown.nativeElement, 'scrollTop', itemIndex * itemHeight);
        }
      }
    }
  }

  ngOnInit() {
    if (!this.textNoResults) {
      this.textNoResults = 'No results found';
    }
  }

  ngAfterContentInit() {
    this.highlightRow(0);
  }
}
