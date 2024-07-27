<?php

require __DIR__ . '/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

include '../../../wp-load.php';

$ot_id = $_GET['id'];

$html = '';

// page-1
$html .= "<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'>
    <div style='border: 5px solid black; height: 98%;'>
    </div>
</page>";

// page-2
$html .= "<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'>
    <div style='border: 5px solid black; height: 98%;'>
        <h4 style='text-align: center;'>TERMS AND CONDITIONS</h4>
        <table style='width: 104%; font-size: 9.35px; text-align: justify;'>
            <tr>
                <td class='left-column' style='width: 48%; padding-left: 5px;'>
                    <b>1. Owner’s Responsibilities.</b> The Owner is responsible to supply water,
                        gas, sewer and electrical utilities unless otherwise agreed to in writing.
                        Electricity and water to the site is necessary. Owner agrees to allow and
                        provide Contractor and his equipment access to the property. The Owner
                        is responsible for having sufficient funds to comply with this agreement.
                        This is a cash transaction unless otherwise specified. The Owner is
                        responsible to remove or protect any personal property and Contractor is
                        not responsible forsame or for any carpets, drapes, furniture, driveways,
                        lawns, shrubs, etc. The Owner shall point out and warrant the property
                        lines to Contractor, and shall hold Contractor harmless for any disputes
                        or errors in the property line or setback locations.
                    <br>
                    <b>2. Delays.</b> Contractor agrees to start and diligently pursue work through
                        to completion, but shall not be responsible for delays for any of the following
                        reasons: failure of the issuance of all necessary building permits within
                        a reasonable length of time, funding of loans, disbursement of funds into
                        control or escrow, acts of neglect or omission of Owner or Owner’s
                        employees or Owner’s agent, acts of God, stormy or inclement weather,
                        strikes, lockouts, boycotts or other labor union activities, extra work ordered
                        by Owner, acts of public enemy, riots or civil commotion, inability to secure
                        material through regular recognized channels, imposition of Government
                        priority or allocation of materials, failure of Owner to make payments when
                        due, or delays caused by inspection or changes ordered by the inspectors
                        of authorized Governmental bodies, or for acts of independent Contractors,
                        or other causes beyond Contractor’s reasonable control.
                    <br>
                    <b>3. Plans and Specifications.</b> If plans and specifications are prepared
                        for this job, they shall be attached to and become a part of theAgreement.
                        Contractor will obtain and pay for all required building permits, but
                        Owner will pay assessments and charges required by public bodies and
                        utilities for financing or repaying the cost of sewers, storm drains, water
                        service, other utilities, water hook-up charges and the like.
                    <br>
                    <b>4. Subcontracts</b> The Contractor may subcontract portions of this
                        work to properly licensed and qualified subcontractors.
                    <br>
                    <b>5. Completion and Occupancy.</b> . Owner agrees to sign and record a
                        notice of completion within five days after the project is complete and
                        ready for occupancy. If the project passes final inspection by the public
                        body but Owner fails to record Notice of Completion, then Owner hereby
                        appoints Contractor as Owner’s agent to sign and record a Notice of
                        Completion on behalf of Owner.
                        This agency is irrevocable and is an agency coupled with an interest.
                        In the event the Owner occupies the project or any part thereof before
                        the Contractor has received all payment due under this contract, such
                        occupancy shall constitute full and unqualified acceptance of all the
                        Contractor’s work by the Owner and the Owner agrees that such
                        occupancy shall be a waiver of any and all claims against the Contractor.
                    <br>
                    <b>6. Insurance and Deposits.</b> Owner will procure at his own expense
                        and before the commencement of any work hereunder, fire insurance
                        with course of construction, vandalism and malicious mischief clauses
                        attached, such insurance to be a sum at least equal to the contract price
                        with loss, if any, payable to any beneficiary under any deed of trust
                        covering the project, such insurance to name the Contractor and his
                        subcontractors as additional insured, and to protect Owner, Contractor
                        and his subcontractors and construction lender as their interests may
                        appear; should Owner fail to do so, Contractor may procure such insurance
                        as agent for and at the expense of Owner, but is not required to do so.
                        If the project is destroyed or damaged by disaster, accident or calamity,
                        such as fire, storm, earthquake, flood, landslide, or by theft or vandalism,
                        any work done by the Contractor rebuilding or restoring the project
                        shall be paid by the Owner as extra work.
                        Contractor shall carry Worker’s Compensation Insurance for the
                        protection of Contractor’s employees during the progress of the work.
                        Owner shall obtain and pay for insurance against injury to his own
                        employees and persons under Owner’s discretion and persons on
                        the job site at Owner’s invitation
                    <br>
                    <b>7. Right to Stop Work.</b> Contractor shall have the right to stop work if any
                        payment shall not be made, when due, to Contractor under this agreement;
                        Contractor may keep the job idle until all payments due are received. Such
                        failure to make payment, when due, is a material breach of thisAgreement.
                        Overdue payments will bear interest at the rate of 1½% per month
                        (18% per annum).
                    <br>
                    <b>8. Clean Up.</b> Contractor will remove from Owner’s property debris
                        and surplus material created by his operation and leave it in a neat
                        and broom clean condition.
                    <br>
                    <b>9. Limitations.</b> No action of any character arising from or related
                        to this contract, or the performance thereof, shall be commenced by
                        either party against the other more than two years after completion
                        or cessation of work under this contract.
                    <br>
                    <b>10. Validity and Damages.</b> In case one or more of the provisions of
                        this Agreement or any application thereof shall be invalid, unenforceable
                        or illegal, the validity, enforceability and legality of the remaining
                        provisions and any other applications shall not in any way be impaired
                        thereby.Any damages for which Contractor may be liable to Owner
                        shall not, in any event, exceed the cash price of this contract.
                    <br>
                    <b>11. Asbestos, Lead, Mold, and other Hazardous Materials.</b>  Owner
                        hereby represents that Owner has no knowledge of the existence on or in
                        any portion of the premises affected by the Project of any asbestos, lead
                        paint, mold (including all types of microbial matter or microbiological
                        contamination, mildew or fungus), or other hazardous materials.
                        Testing for the existence of mold and other hazardous materials
                        shall only be performed as expressly stated in writing.
                        Contractor shall not be testing or performing any work whatsoever
                        in an area that is not identified in the Scope of Work.
                        Unless the contract specifically calls for the removal, disturbance, or
                        transportation of asbestos, polychlorinated biphenyl (PCB), mold,
                </td>
                <td class='right-column' style='width: 48%; padding-right: 5px;'>
                        lead paint, or other hazardous substances or materials, the parties
                        acknowledge that such work requires special procedures, precautions,
                        and/or licenses. Therefore, unless the contract specifically calls for
                        same, if Contractor encounters such substances, Contractor shall
                        immediately stop work and allow the Owner to obtain a duly qualified
                        asbestos and/or hazardous material contractor to perform the work or
                        Contractor may perform the work itself at Contractor’s option. Said work
                        will be treated as an extra under this contract, and the Contract Term
                        setting forth the time for completion of the project may be delayed.
                        In the event that mold or microbial contamination is removed by
                        Contractor, Owner understands and agrees that due to the unpredictable
                        characteristics of mold and microbial contamination, Contractor shall
                        not be responsible for any recurring incidents of mold or microbial
                        contamination appearing in the same or any adjacent location, subsequent
                        to the completion of the work performed by Contractor. Owner agrees to
                        hold Contractor harmless, and shall indemnify Contractor harmless for
                        any recurrence of mold or microbial contamination. Owner also agrees
                        that Contractor shall not be responsible, and agrees to hold Contractor
                        harmless and indemnify Contractor, for the existence of mold or microbial
                        contamination in any area that Contractor was not contracted to test and/or
                        remediate. Further, Owner is hereby informed, and hereby acknowledges,
                        that most insurers expressly disclaim coverage for any actual or alleged
                        damages arising from mold or microbial contamination.
                        Contractor makes no representations whatsoever as to coverage for mold
                        contamination, though at Owner’s additional expense, if requested in
                        writing, Contractor will inquire as to the availability of additional coverage
                        for such contamination or remediation, and if available, will obtain such
                        coverage if the additional premium is paid for by Owner as an extra.
                    <br>
                    <b>12. Standards of Materials and Workmanship.</b> Contractor shall use
                        and install “standard grade” or “builder’s grade” materials on the project
                        unless otherwise stated in the Scope of Work, the plans, and/or
                        specifications provided to Contractor prior to the execution of this
                        Agreement. Unless expressly stated in the Scope of Work, Contractor shall
                        have no liability or responsibility to restore or repair the whole or any part
                        of the premises affected by the work of Contractor to be performed
                        herein or by any subsequently agreed-upon change order, including as
                        an illustration and not as a limitation, any landscaping, sprinkler system,
                        flooring and carpet, wall coverings, paint, tile, or decorator items.
                    <br>
                    <b>13. Limited Warranty.</b> Contractor warrants that all work
                        performed by it and its subcontractors shall be done in a good
                        and workmanlike manner in accordance with accepted trade
                        practices. Said warranty shall extend for one year from the date of
                        substantial completion of Contractor’s portion of the project.
                        However, the warranties for assemblies, appliances and the like,
                        shall be those warranties provided by the manufacturer or supplier
                        of that item rather than based on Contractor’s warranty herein.
                        Contractor shall assemble and provide to Owner all such
                        manufacturer’s warranties.
                    <br>
                    <b>14. Changes in the Work - Concealed Conditions.</b> Should the
                        Owner, construction lender, or any public body or inspector direct any
                        modification or addition to the work covered by this contract, the
                        contract price shall be adjusted accordingly.
                        Modification or addition to the work shall be executed only when a
                        Contract Change Order has been signed by both the Owner and the
                        Contractor. The change in the Contract Price caused by such Contract
                        Change Order shall be as agreed to in writing, or if the parties are not in
                        agreement as to the change in Contract Price, the Contractor’s actual cost of
                        all labor, equipment, subcontracts and materials, plus a Contractor’s
                        fee of 20% or % shall be the change in Contract Price.
                        The Contract Change Order may also increase the time within
                        which the contract is to be completed.
                        Contractor shall promptly notify the Owner of (a) subsurface or
                        latent physical conditions at the site differing materially from those
                        indicated in the contract, or (b) unknown physical conditions
                        differing materially from those ordinarily encountered and generally
                        recognized as inherent in work of the character provided for in this
                        contract. Any expense incurred due to such conditions shall be paid for
                        by the Owner as added work. Payment for extra work will be made
                        as extra work progresses.
                    <br>
                    <b>15. Fees, Taxes and Assessments, Compliance With Laws.</b> Taxes, Permits, Fees, and assessments of all descriptions will
                        be paid for by Owner. Contractor will obtain all required
                        building permits, at the sole expense of Owner. Upon demand by
                        Contractor, Owner shall provide ample funds to acquire any and all
                        necessary permits on a timely basis. Owner will pay assessments
                        and charges required by public bodies and utilities for financing or
                        repaying the cost of sewers, storm drains, water service, schools
                        and school facilities, other utilities, hook-up charges and the like.
                        Contractor shall comply with all federal, state, county and local
                        laws, ordinances and regulations.
                    <br>
                    <b>16. Labor and Material.</b>  Contractor shall pay all valid charges
                        for labor and material incurred by Contractor and used in the
                        construction or repair of the Project. Contractor is excused from this
                        obligation for bills received in any period during which the Owner is
                        in arrears in making progress payments to Contractor No waiver or
                        release of mechanic’s lien given by Contractor shall be binding until
                        all payments due to Contractor when the release was executed have
                        been made.
                    <br>
                    <b>17. Right to Cure.</b> In the event that Owner alleges that some of
                        the work is not or has not been done correctly or timely, Owner
                        shall give Contractor a notice that Contractor shall commence to
                        cure the condition that Owner has alleged is insufficient within ten
                        days.
                </td>
            </tr>
        </table>
    </div>
</page>";

// page-3
$html .= "<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'></page>";

// page-4
$html .= "<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'></page>";

// page-5
$html .= "<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'></page>";

$orientation = 'potrait';
$titulo_pdf = 'Contract';
$html2pdf = new Html2Pdf($orientation, 'LETTER', 'es');
$html2pdf->writeHTML($html);
$html2pdf->output($titulo_pdf . '_000' . $ot_id . '.pdf');