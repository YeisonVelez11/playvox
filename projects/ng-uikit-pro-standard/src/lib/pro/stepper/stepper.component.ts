import {
  Component,
  ViewEncapsulation,
  ContentChildren,
  QueryList,
  OnInit,
  AfterContentInit,
  HostListener,
  Input,
  ElementRef,
  ViewChild,
  ViewChildren,
  AfterViewInit,
  Renderer2,
  PLATFORM_ID,
  Inject
} from '@angular/core';
import { MdbStepComponent } from './step.component';
import { trigger, state, style, transition, animate } from '@angular/animations';
import { window } from './../../free/utils/facade/browser';
import { WavesDirective } from '../../free/waves/waves-effect.directive';
import { FormControl } from '@angular/forms';
import { isPlatformBrowser } from '@angular/common';

@Component({
  selector: 'mdb-stepper',
  exportAs: 'mdbStepper',
  templateUrl: 'stepper.component.html',
  encapsulation: ViewEncapsulation.None,
  animations: [trigger('stepContentTransition', [
    state('previous', style({transform: 'translateX(-100%)', visibility: 'hidden'})),
    state('next', style({transform: 'translateX(100%)', visibility: 'hidden'})),
    state('current', style({transform: 'none', visibility: 'visible'})),
    transition('* => *', animate('600ms ease'))
  ])],
  providers: [WavesDirective]
})
export class MdbStepperComponent implements OnInit, AfterContentInit, AfterViewInit {
  @ContentChildren(MdbStepComponent) steps: QueryList<MdbStepComponent>;
  @ViewChildren('stepTitle') stepTitles: QueryList<ElementRef>;
  @ViewChildren('stepContent') stepContents: QueryList<ElementRef>;
  @ViewChild('container') container: ElementRef;
  @Input() linear = false;
  @Input() disableWaves = false;
  @Input() vertical = false;

  constructor(
    public ripple: WavesDirective,
    private _renderer: Renderer2,
    @Inject(PLATFORM_ID) platformId: string) {
      this.isBrowser = isPlatformBrowser(platformId);
    }

  isBrowser: boolean;
  horizontal = true;
  private _stepperBreakpoint = 992;

  get activeStepIndex() { return this._activeStepIndex; }
  set activeStepIndex(value: number) {
    this._activeStepIndex = value;
  }
  private _activeStepIndex: number;
  private _activeStep: MdbStepComponent;

  @HostListener('window:resize')
  onWindowResize() {
    if (this.isBrowser) {
      if (window.innerWidth < this._stepperBreakpoint) {
        this.horizontal = false;
        this._updateHorizontalStepperHeight(this.activeStepIndex);
      } else {
        this.horizontal = true;
        this._updateHorizontalStepperHeight(this.activeStepIndex);
      }
    }
  }

  onClick(index: number, event: any) {
    if (!this.disableWaves) {
      const clickedEl = this.stepTitles.toArray()[index];
      this.ripple.el = clickedEl;
      this.ripple.click(event);
    }
  }

  ngOnInit() {
  }

  private _isStepValid(step: MdbStepComponent) {
    if (!step.stepForm) {
      return true;
    }

    if (step.stepForm && step.stepForm.valid) {
      return true;
    }

    return false;
  }

  getAnimationState(index: number): string {
    const nextElPosition = index - this.activeStepIndex;
    if (nextElPosition < 0) {
      return 'previous';
    } else if (nextElPosition > 0) {
      return 'next';
    }
    return 'current';
  }

  private _getStepByIndex(index: number): MdbStepComponent {
    return this.steps.toArray()[index];
  }

  next() {
    if (this.activeStepIndex < (this.steps.length - 1)) {
        this.setNewActiveStep(this.activeStepIndex + 1);
    }
  }

  previous() {
    if (this.activeStepIndex > 0) {
      this.setNewActiveStep(this.activeStepIndex - 1);
    }
  }

  submit() {
    if (this.linear) {
      this._markCurrentAsDone();
    }
  }

  setNewActiveStep(index: number) {
    const newStep = this._getStepByIndex(index);

    if (this.linear && !this._isNewStepLinear(index)) {
      return;
    }

    this._removeStepValidationClasses(newStep);

    if (this.linear && index > this.activeStepIndex) {
      if (this._isStepValid(this._activeStep)) {
        this._markCurrentAsDone();
        this._removeCurrentActiveStep();
        this._setActiveStep(index);
      } else {
        this._markCurrentAsWrong();
        this._markStepControlsAsDirty(this._activeStep);
      }
    } else {
      if (index < this.activeStepIndex) {
        this._removeStepValidationClasses(this._activeStep);
      }

      this._removeCurrentActiveStep();
      this._setActiveStep(index);
    }

  }

  private _markCurrentAsDone() {
    this._activeStep.isDone = true;
    this._activeStep.isWrong = false;
  }

  private _markCurrentAsWrong() {
    this._activeStep.isWrong = true;
    this._activeStep.isDone = false;
  }

  private _markStepControlsAsDirty(step: MdbStepComponent) {
    const controls = step.stepForm.controls;
    if (step.stepForm.controls) {
      const keys = Object.keys(controls);
      for (let i = 0; i < keys.length; i++) {
        const control = controls[keys[i]];

        if (control instanceof FormControl) {
          control.markAsTouched();
        }
      }
    }
  }

  private _removeStepValidationClasses(step: MdbStepComponent) {
    step.isDone = false;
    step.isWrong = false;
  }

  private _isNewStepLinear(newStepIndex: number) {
    return this.activeStepIndex - newStepIndex === 1 || this.activeStepIndex - newStepIndex === -1;
  }

  private _setActiveStep(index: number) {
    this.steps.toArray()[index].isActive = true;
    this._updateHorizontalStepperHeight(index);
    this.activeStepIndex = index;
    this._activeStep = this._getStepByIndex(this.activeStepIndex);
  }

  private _removeCurrentActiveStep() {
    const currentActiveStep = this.steps.find(activeStep => activeStep.isActive);
    if (currentActiveStep) {
      currentActiveStep.isActive = false;
    }
  }

  resetAll() {
    this.steps.forEach( (step: MdbStepComponent) => {
      step.reset();
      this._setActiveStep(0);
    });
  }

  private _updateHorizontalStepperHeight(index: number) {
    if (this.horizontal && !this.vertical) {
      setTimeout(() => {
        const height = this.stepContents.toArray()[index].nativeElement.scrollHeight + 50;
        this._renderer.setStyle(this.container.nativeElement, 'height', height + 'px');
      }, 0);
    } else {
        this._renderer.removeStyle(this.container.nativeElement, 'height');
    }
  }

  private _initStepperVariation() {
    if (this.isBrowser) {
      if (this.vertical || window.innerWidth < this._stepperBreakpoint) {
        setTimeout(() => {
          this.horizontal = false;
          this._renderer.removeStyle(this.container.nativeElement, 'height');
        }, 0);
      }
    }
  }

  ngAfterViewInit() {
    this._initStepperVariation();
  }

  ngAfterContentInit() {
    this._setActiveStep(0);
  }
}
