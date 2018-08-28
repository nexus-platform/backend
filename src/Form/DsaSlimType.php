<?php

namespace App\Form;

use App\Entity\DsaSlim;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DsaSlimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_reference_number', TextType::class, [
                'required' => '',
                'label' => 'Customer Reference Number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('forename', TextType::class, [
                'required' => '',
                'label' => 'Forename(s)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('surname', TextType::class, [
                'required' => '',
                'label' => 'Surname',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    'Male' => 'Male',
                    'Famale' => 'Famale'
                ],
                'label' => 'Sex',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dobDay', TextType::class, [
                'required' => '',

                'label' => 'Date of birth - Day',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dobMonth', TextType::class, [
                'required' => '',

                'label' => 'Date of birth - Month',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dobYear', TextType::class, [
                'required' => '',

                'label' => 'Date of birth - Year',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('excluding', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'A Department of Health or NHS Bursary excluding the
	 Social Work Bursary paid by the NHS Business Services
	 Authority',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('saas', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'A bursary from Student Awards Agency Scotland
	 (SAAS)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('healthcare', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'A Healthcare Bursary from the Department of Health
	 (Northern Ireland)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('receipt', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'Are you in receipt of the mobility component of Disability
Living Allowance or Personal Independence Payment?',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('motability_car', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'Do you use this to lease a motability car?',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('disabilitydetails', TextType::class, [
                'required' => '',

                'label' => 'Please give full details of your disability. You should include the name or diagnoses of your disability.',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('disabilitydetailsfile', FileType::class, [
                'label' => 'Please provide photocopied evidence of your disability',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('long_termadverse_effect', FileType::class, [

                'label' => 'You should provide a written statement or letter from a doctor or appropriate
qualified medical professional which confirms a substantial and long term
adverse effect on your ability to carry out normal day-to-day activities.',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('learning_difficulty', FileType::class, [

                'label' => 'You should provide a post-16 diagnostic report, written in accordance with the
2005 Specific learning difficulty (SpLD) Working Group Guidelines, from either:
A registered psychologist or
A suitably qualified specialist teacher, holding a SpLD Assessment Practicing
Certificate.',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('autistic_spectrum_disorders', FileType::class, [

                'label' => 'You should provide a written statement or letter from a doctor or appropriate
qualified medical professional which confirms a substantial and long term
adverse effect on your ability to carry out normal day-to-day activities
or
Statement of Special Educational Needs (SEN) issued by a Local Authority',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('laDay', TextType::class, [
                'required' => '',

                'label' => 'Date of assessment - Day',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('laMonth', TextType::class, [
                'required' => '',

                'label' => 'Date of assessment - Month',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('laYear', TextType::class, [
                'required' => '',

                'label' => 'Date of assessment - year',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pc', ChoiceType::class, [
                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'Do you currently own a laptop or
		 desktop computer?',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('working_order', ChoiceType::class, [

                'choices' => [
                    'Yes' => 'Yes',
                    'No' => 'No'
                ],
                'label' => 'To the best of your knowledge is the computer in
good working order?',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('model', TextType::class, [
                'required' => '',

                'label' => 'Make and model
(for example - Toshiba Satellite Pro
C50-A-1MM)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('age', TextType::class, [
                'required' => '',

                'label' => 'Approximate age of laptop or desktop
computer:',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('processor', TextType::class, [
                'required' => '',

                'label' => 'Processor
(for example - Intel i3 4160 3.60Ghz)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('agree1', CheckboxType::class, [
                'required' => '',


                'label' => 'I agree that Student Finance England and the disability service at my
university or college may exchange information about my application for
DSAs where this is necessary to make sure I get the help I need.'
            ])
            ->add('agree2', CheckboxType::class, [
                'required' => '',


                'label' => 'I agree that Student Finance England and my DSAs Needs Assessor may
exchange information about my application for DSAs where this is necessary
to make sure I get the help I need.'
            ])
            ->add('agree3', CheckboxType::class, [
                'required' => '',


                'label' => 'I agree that Student Finance England and my DSAs suppliers may exchange
information about my application for DSAs where this is necessary to make
sure I get the help I need.'
            ])
            ->add('sortcode', TextType::class, [
                'required' => '',

                'label' => 'Sort code',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('accountnumber', TextType::class, [
                'required' => '',

                'label' => 'Account number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('building', TextType::class, [
                'required' => '',

                'label' => 'Building society roll number
(if applicable)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fullname', TextType::class, [
                'required' => '',

                'label' => 'Your full name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('todayDay', TextType::class, [
                'required' => '',

                'label' => 'Today Day',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('todayMonth', TextType::class, [
                'required' => '',

                'label' => 'Today Month',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('todayYear', TextType::class, [
                'required' => '',

                'label' => 'Today Year',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('signed', CheckboxType::class, [
                'required' => '',

                'label' => 'Signed and dated the declaration.',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => '',
                'attr' => [
                    'class' => 'btn btn-lg btn-primary btn-block'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class' => DsaSlim::class
        ]);
    }
}
