import {
  Directive,
  ContentChildren,
  AfterContentInit,
  QueryList,
  Input,
  OnDestroy,
  OnInit,
  EventEmitter,
  Output
} from '@angular/core';
import {ScrollSpyLinkDirective} from './scroll-spy-link.directive';
import {ScrollSpyService} from './scroll-spy.service';
import {distinctUntilChanged} from 'rxjs/operators';
import {Subscription} from 'rxjs';

@Directive({
  selector: '[mdbScrollSpy]'
})
export class ScrollSpyDirective implements OnInit, AfterContentInit, OnDestroy {
  @ContentChildren(ScrollSpyLinkDirective, {descendants: true})
  links: QueryList<ScrollSpyLinkDirective>;

  @Input('mdbScrollSpy')
  get id(): string {
    return this._id;
  }

  set id(newId: string) {
    if (newId) {
      this._id = newId;
    }
  }

  private _id: string;

  @Output() activeLinkChange: EventEmitter<any> = new EventEmitter<any>();

  activeSub: Subscription;

  constructor(private scrollSpyService: ScrollSpyService) {
  }

  ngOnInit() {
    this.activeSub = this.scrollSpyService.active$
      .pipe(distinctUntilChanged())
      .subscribe((activeLink) => {
        this.activeLinkChange.emit(activeLink);
      });
  }

  ngAfterContentInit() {
    this.scrollSpyService.addScrollSpy({id: this.id, links: this.links});
  }

  ngOnDestroy() {
    this.scrollSpyService.removeScrollSpy(this.id);
    this.activeSub.unsubscribe();
  }
}
