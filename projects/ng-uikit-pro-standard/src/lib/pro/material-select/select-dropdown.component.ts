import {
  AfterViewInit,
  Component,
  EventEmitter,
  Input,
  OnChanges,
  OnInit,
  Output,
  ViewChild,
  ViewEncapsulation, ElementRef, HostListener, Renderer2, ChangeDetectorRef, ChangeDetectionStrategy, SimpleChanges
} from '@angular/core';
import {trigger, state, style, transition, animate} from '@angular/animations';
import {Option} from './option';
import {OptionList} from './option-list';

@Component({
  selector: 'mdb-select-dropdown',
  templateUrl: 'select-dropdown.component.html',
  encapsulation: ViewEncapsulation.None,
  changeDetection: ChangeDetectionStrategy.Default,
  animations: [trigger('dropdownAnimation', [
    state('invisible', style({opacity: 0, height: '0px'})),
    state('visible', style({opacity: 1, height: '*'})),
    transition('invisible => visible', animate('300ms ease')),
    transition('visible => invisible', animate('300ms ease'))
  ])]
})
export class SelectDropdownComponent
  implements AfterViewInit, OnChanges, OnInit {

  @Input() filterEnabled: boolean;
  @Input() highlightColor: string;
  @Input() highlightTextColor: string;
  @Input() left: number;
  @Input() multiple: boolean;
  @Input() notFoundMsg: string;
  @Input() optionList: OptionList;
  @Input() top: number;
  @Input() width: number;
  @Input() placeholder: string;
  @Input() customClass = '';
  @Input() visibleOptions = 4;
  @Input() dropdownHeight: number;
  @Input() dropdownMaxHeight: number;
  @Input() optionHeight: number;
  @Input() enableSelectAll: boolean;
  @Input() selectAllLabel = 'Select all';
  @Output() close = new EventEmitter<boolean>();
  @Output() optionClicked = new EventEmitter<Option>();
  @Output() singleFilterClick = new EventEmitter<null>();
  @Output() singleFilterInput = new EventEmitter<string>();
  @Output() singleFilterKeydown = new EventEmitter<any>();
  @Output() animationDone = new EventEmitter<any>();
  @Output() animationStart = new EventEmitter<any>();
  @Output() selectAll = new EventEmitter<boolean>();

  @ViewChild('filterInput') filterInput: any;
  @ViewChild('optionsList') optionsList: any;
  @ViewChild('dropdownContent') dropdownContent: ElementRef;

  disabledColor = '#fff';
  disabledTextColor = '9e9e9e';

  // Used in sliding-down animation
  state = 'invisible';
  startHeight: any = 0;
  endHeight: any = 45;

  public hasOptionsItems = true;

  selectAllSelected = false;

  constructor(
    public _elementRef: ElementRef,
    public _renderer: Renderer2,
    private cdRef: ChangeDetectorRef) {
  }

  /** Event handlers. **/

  // Angular life cycle hooks.

  @HostListener('keyup') onkeyup() {
    this.hasOptionsItems = this.optionList.filtered.length > 0;
    this.updateSelectAllState();
  }

  @HostListener('input') onkeydown() {
    this.setOptionHeight();
  }

  ngOnInit() {
    this.updateSelectAllState();
    this.optionsReset();
    this.setDropdownHeight();
    this.setVisibleOptionsNumber();
  }

  setDropdownHeight() {
    this.optionList.options.filter(el => () => {
      if (el.icon) {
        this._renderer.setStyle(this.optionsList.nativeElement, 'height', (this.dropdownHeight + 8) + 'px');
      } else {
        this._renderer.setStyle(this.optionsList.nativeElement, 'height', this.dropdownHeight + 'px');
      }
    });
  }

  setVisibleOptionsNumber() {
    this._renderer.setStyle(this.optionsList.nativeElement, 'max-height', this.dropdownMaxHeight + 'px');
  }

  setOptionHeight() {
    const optionsItems = Array.from(this.optionsList.nativeElement.firstElementChild.children);
    optionsItems.forEach((el: any) => {
      if (this.optionHeight && el.firstElementChild.tagName !== 'IMG') {
        this._renderer.setStyle(el.firstElementChild, 'height', `${this.optionHeight}px`);
      }
      if (el.firstElementChild.tagName !== 'IMG') {
        this._renderer.setStyle(el.firstElementChild, 'line-height', `${this.optionHeight}px`);
      }
    });
  }

  ngOnChanges(changes: SimpleChanges) {
    if (changes.hasOwnProperty('optionList')) {
      this.optionsReset();
    }

    if (changes.hasOwnProperty('dropdownHeight')) {
      this.setDropdownHeight();
    }

    const container = this._elementRef.nativeElement.classList;
    setTimeout(() => {
      container.add('fadeInSelect');
    }, 200);
  }

  ngAfterViewInit() {
    // Sliding-down animation
    this.endHeight = this.dropdownContent.nativeElement.clientHeight;
    this.state = (this.state === 'invisible' ? 'visible' : 'invisible');
    this.cdRef.detectChanges();

    if (this.multiple) {
      const disabledElements = this._elementRef.nativeElement.querySelectorAll('.disabled.optgroup');

      for (let i = 0; i < disabledElements.length; i++) {
        this._renderer.setStyle(disabledElements[i].firstElementChild.lastElementChild, 'display', 'none');
      }
    }

    this.setOptionHeight();

    this.moveHighlightedIntoView();
    if (this.filterEnabled) {
      setTimeout(() => {
        this.filterInput.nativeElement.focus();
      }, 0);
    }
  }

  // Filter input (single select).

  onSingleFilterClick() {
    this.singleFilterClick.emit(null);
  }

  onSingleFilterInput(event: any) {
    this.singleFilterInput.emit(event.target.value);
  }

  onSingleFilterKeydown(event: any) {
    this.singleFilterKeydown.emit(event);
  }

  // Options list.

  onOptionsWheel(event: any) {
    this.handleOptionsWheel(event);
  }

  onOptionClick(option: Option) {
    this.optionClicked.emit(option);
    this.updateSelectAllState();
  }

  /** Initialization. **/

  private optionsReset() {
    this.optionList.filter('');
    this.optionList.highlight();
  }

  /** View. **/

  getOptionStyle(option: Option): any {
    if (option.highlighted || option.hovered) {
      const optionStyle: any = {};
      optionStyle['height.px'] = this.optionHeight;
      if (typeof this.highlightColor !== 'undefined') {
        optionStyle['background-color'] = this.highlightColor;
      }
      if (typeof this.highlightTextColor !== 'undefined') {
        optionStyle['color'] = this.highlightTextColor;
      }
      return optionStyle;
    } else {
      return {};
    }
  }

  onSelectAllClick() {
    this.selectAllSelected = !this.selectAllSelected;
    this.selectAll.emit(this.selectAllSelected);
  }

  updateSelectAllState() {
    const areAllSelected = this.optionList.filtered.every((option: Option) => {
      return option.selected ? true : false;
    });

    areAllSelected ? this.selectAllSelected = true : this.selectAllSelected = false;
    this.cdRef.detectChanges();
  }

  clearFilterInput() {
    if (this.filterEnabled) {
      this.filterInput.nativeElement.value = '';
    }
  }

  onAnimationDone() {
    this.animationDone.emit();
  }

  onAnimationStart() {
    this.animationStart.emit();
  }

  moveHighlightedIntoView() {
    let listHeight: number;
    const list = this.optionsList.nativeElement;
    listHeight = this.multiple && this.enableSelectAll ? list.offsetHeight - this.optionHeight : list.offsetHeight;

    const itemIndex = this.optionList.getHighlightedIndex();

    if (itemIndex > -1) {
      const item = list.children[0].children[itemIndex];
      const itemHeight = item.offsetHeight;

      const itemTop = itemIndex * itemHeight;
      const itemBottom = itemTop + itemHeight;

      const viewTop = list.scrollTop;
      const viewBottom = viewTop + listHeight;

      if (itemBottom > viewBottom) {
        list.scrollTop = itemBottom - listHeight;
      } else if (itemTop < viewTop) {
        list.scrollTop = itemTop;
      }

    }
  }

  private handleOptionsWheel(e: any) {
    const div = this.optionsList.nativeElement;
    const atTop = div.scrollTop === 0;
    const atBottom = div.offsetHeight + div.scrollTop === div.scrollHeight;

    if (atTop && e.deltaY < 0) {
      e.preventDefault();
    } else if (atBottom && e.deltaY > 0) {
      e.preventDefault();
    }

  }

}
